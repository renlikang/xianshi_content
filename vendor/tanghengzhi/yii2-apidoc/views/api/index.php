<?php
use tanghengzhi\apidoc\assets\SwaggerAsset;
SwaggerAsset::register($this);
/** @var string $rest_url */
/** @var array $oauthConfig */
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>APIDOC</title>
		<script>
            function log() {
                if ('console' in window) {
                    console.log.apply(console, arguments);
                }
            }
		</script>
        <?php $this->head() ?>
        <script type="text/javascript">
            $(function () {
                var url = window.location.search.match(/url=([^&]+)/);
                if (url && url.length > 1) {
                    url = decodeURIComponent(url[1]);
                } else {
                    url = "<?= $rest_url ?>";
                }

                hljs.configure({
                    highlightSizeThreshold: 5000
                });

                // Pre load translate...
                if(window.SwaggerTranslator) {
                    window.SwaggerTranslator.translate();
                }
                window.swaggerUi = new SwaggerUi({
                    url: url,
                    dom_id: "swagger-ui-container",
                    supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
                    onComplete: function(swaggerApi, swaggerUi){
                        if(typeof initOAuth == "function") {
                            initOAuth(<?= json_encode($oauthConfig) ?>);
                        }

                        if(window.SwaggerTranslator) {
                            window.SwaggerTranslator.translate();
                        }
                    },
                    onFailure: function(data) {
                        log("Unable to Load SwaggerUI");
                    },
                    docExpansion: "none",
                    jsonEditor: false,
                    defaultModelRendering: 'schema',
                    showRequestHeaders: false,
                    showOperationIds: false,
                    validatorUrl: undefined,
                    translator: true
                });

                window.swaggerUi.load();
            });
        </script>
    </head>
    
    <body class="swagger-section">
    <?php $this->beginBody() ?>
    <div id="message-bar" class="swagger-ui-wrap" data-sw-translate>&nbsp;</div>
    <div id="swagger-ui-container" class="swagger-ui-wrap"></div>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>