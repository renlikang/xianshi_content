<?php
namespace api\controllers;

use common\models\content\ArticleModel;
use common\models\elasticsearch\ArticleElasticModel;
use common\models\elasticsearch\ArticleRemote;
use common\models\elasticsearch\Book;
use common\models\elasticsearch\WxForm;
use common\models\User;
use common\services\FileServices;
use common\services\UserService;
use common\traits\TraitThrift;
use linslin\yii2\curl\Curl;
use yii\rest\Controller;
use Spipu\Html2Pdf\Html2Pdf;
use Yii;

/**
 * Site controller
 */
class SiteController extends Controller
{
    use TraitThrift;
    /**
     * @SWG\Post(
     *     path="/site/login",
     *     tags={"测试功能"},
     *     summary="登录",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "userName",description = "优雅的狗， 教主， 绿森林的小魔女",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionLogin()
    {
        if(YII_ENV_PROD == true) {
            return true;
        }

        $token = md5(time());
        $userName = Yii::$app->request->post('userName');
        $user = User::findOne(['nickName' => $userName]);

        $userInfo = [
            'id' => $user->id,
            'token' => $token,
            'is_new' => 0,
            'expire' => time() + 24*60*60
        ];

        foreach ($userInfo as $key => $value) {
            \Yii::$app->sessionCache->hset($token, $key, $value);
        }
        \Yii::$app->sessionCache->expire($token, 24*60*60);

        $userInfo['token'] = $token;
        return $userInfo;
    }
    public function actionTest()
    {
        var_dump(Yii::$app->request->hostInfo);
        exit;
        $a = new Curl;
        $a->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $a->setOption(CURLOPT_SSL_VERIFYHOST, false);

        var_dump($a->get('https://static.fashionipo.com/instagram/2018/10/12/7fa3fd0c3c8e4593ae88c9e627c30a4a.jpg'));exit;
//        $client = new \Raven_Client('https://b48439c09ac54c06a84f297a46d3eec2@sentry.heywoof.com/2');
//        $error_handler = new \Raven_ErrorHandler($client);
//        $error_handler->registerExceptionHandler();
//        $error_handler->registerErrorHandler();
//        $error_handler->registerShutdownFunction();
//        $client->captureMessage("这里发生了一个错误");

        Yii::error("api错误", __CLASS__.'::'.__FUNCTION__);
        exit;
        //var_dump($client);exit;

        phpinfo();
        exit;
        $html2pdf = new Html2Pdf();
        $html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first test');
        $html2pdf->output();
        exit;
    }

    public static function pdfToPng($pdf,$path,$page=-1)
    {
        if (!extension_loaded('imagick')) {
            return 1;
        }
        if (!file_exists($pdf)) {
            return 2;
        }
        if (!is_readable($pdf)) {
            return 3;
        }
        $im = new \Imagick();
        $im->setResolution(150, 150);
        $im->setCompressionQuality(100);
        if ($page == -1) {
            $im->readImage($pdf);
        } else {
            $im->readImage($pdf . "[" . $page . "]");
        }

        foreach ($im as $Key => $Var) {
            $Var->setImageFormat('png');
            $filename = $path . md5($Key . time()) . '.png';
            if ($Var->writeImage($filename) == true) {
                $Return[] = $filename;
            }
        }
        //返回转化图片数组，由于pdf可能多页，此处返回二维数组。
        return $Return;
    }
    /**
     * @SWG\Get(
     *     path="/site/index",
     *     tags={"实例"},
     *     summary="文章导入格式",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionIndex()
    {
        if(YII_ENV_PROD == true) {
            return "hello word";
        }

        $a = WxForm::find()->all();
        var_dump(count($a));exit;
        foreach ($a as $k => $v) {
            var_dump($v->form_key);
        }
        exit;

        $content = file_get_contents("https://static.heywoof.com/de04d49a/c2b38f97eb212eba357d933028baabcb.png");
        var_dump(md5($content));
        var_dump(md5_file("https://static.heywoof.com/de04d49a/c2b38f97eb212eba357d933028baabcb.png"));
        exit;
        Yii::$app->antispam_txt->filterText("习近平傻逼");
        exit;
        //$this->loadingThrift();
        $content = [
            'articleInfo' => [
                'title' => '你妈多久没帮你穿袜子了？',
                'subTitle' => '袜子君',
                'summary' => '你妈多久没帮你穿袜子了？',
                'headImg' => 'https://mmbiz.qpic.cn/mmbiz_png/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPmqbASdY9zgdlkRL0mR86cdchdpGcoFaoL97xicZ7CUnqndPbUxjlgIA/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1',
                'orderId' => 10,
                'coverType' => 'image',
                'covers' => [
                    ['type' => 'image', 'url' => 'https://1.jpg', 'previewImage' => ''],
                    ['type' => 'video', 'url' => 'https://1.mod', 'previewImage' => 'https://1.jpg'],
                ],
                'tagNames' => [
                    ['tagName' => 'aaa', 'url' => 'https://mmbiz.qpic.cn/mmbiz_png/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPmqbASdY9zgdlkRL0mR86cdchdpGcoFaoL97xicZ7CUnqndPbUxjlgIA/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['tagName' => 'bbb', 'url' => 'https://mmbiz.qpic.cn/mmbiz_png/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPmqbASdY9zgdlkRL0mR86cdchdpGcoFaoL97xicZ7CUnqndPbUxjlgIA/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],

                ],
                //'covers' => ['https://mmbiz.qpic.cn/mmbiz_png/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPmqbASdY9zgdlkRL0mR86cdchdpGcoFaoL97xicZ7CUnqndPbUxjlgIA/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1', 'https://mmbiz.qpic.cn/mmbiz_png/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPmqbASdY9zgdlkRL0mR86cdchdpGcoFaoL97xicZ7CUnqndPbUxjlgIA/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
            ],
            'paragraph' => [
                [
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_png/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPtFlU25YsD3MoXI9SoIX82Y1ZetVEY5xMT728ntGz6BXAydHGIXpyDA/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                ],

                [
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_png/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPcKC71tuibSCficMcI9SEsXvC5MEyTLmrU1iaiaW3CUhDIPSs5OW0KqMekQ/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                ],
                [
                    ['type' => 'text', 'content' => '一周入秋，国庆假期刚过完，回到学校、办公室，大家的衣着从T恤到衬衫再到毛衣… “（春捂）秋冻派”还穿着T恤短裤的小伙子，与永远要比别人提前进入下一个季节已经披上大袄子的“时髦精”同处一室，精彩纷呈。比起夏天，早秋着装在多样性上必然是更胜一筹。'],

                ],

                [
                    ['type' => 'text', 'content' => '不论你是哪一派，感受到丝丝凉意之后，是不是觉得缺一双袜子？然而，你妈妈多久没给你穿袜子买袜子啦？不如自己买几双好看又高质的袜子吧，保暖的同时足下生风。'],
                ],

                [
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPYeFK0gojbY7V5KbaZzswNvtyy24hsCGJugZBCG7hRScByJEXcOt07w/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPwJCA2Sic0Se20PE9LeJjR46FG9QIVFIib4PfEjHibZ71KhwRSYR5SV8uQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzP9XV9hlYTgWe6aIII4MzCibnFNBjicoibwecPliaxXK1KcLoD6Baxnu3xKg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPEicibqADy1micxNhcm46IpGFx8VXXXgWSiaPIa8QzIDsUSDna6u1hyIJqQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                ],


                [
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPUsb6Ym6Hia7ZbTlRuCACfORz6IukGGOXq1YffX65HVsCWMpYNP9yIPg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                ],

                [
                    ['type' => 'text', 'content' => '#纯色socks，妈妈看了直点头'],
                ],
                [
                    ['type' => 'text', 'content' => '简单的纯色socks，尤其是黑白灰这样的无彩色或是藏蓝深咖这样饱和度低的色彩，衣橱基础款式，着急出门的时候随便抓一双穿也不会出错。在此基础上，刺绣点缀或是袜子上精心设计的提花、纹路，一点点，只要一点点，便可在细节上体现你的态度。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPu3Jt91K4NJ8GsYtaRWcjLFA22OHK89cWPFxEjYiaVPcqnTx0THRBHzg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '刚跟Puma合作推出了联名系列的韩国品牌Ader error这一季也顺带出了两双袜子。经典的“Ader蓝”与万能黑色，配上文字提花、这一季限定的彩色图案，搭配同系列的联名产品是再适合不过了。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPe0xT0or4AFnOwhgPAKYhTgP5uhLgj58Aoic3uCMlmDKbREob2QfX8xw/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPe5ekAAl8MRRmrZYMZ1qicpeI8kIH3gg5lzY7SwmScqZFZwMAnoOjJaA/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '若你不喜欢被各路明星带货带到路上天天见的Acne的T恤帽子和围巾，不然试试这一季Acne的袜子？白底的基础上加入了彩条与标志性的笑脸，清爽又活泼的运动风。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPKUMAZlYgCZwHgMxZWLB6o7WZJE4aK5iciao0DibNjhxmuvia4iaIXQNLxLA/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '运动、街头品牌的出的袜子，字母、标语、Logo、彩条… 即便这些规则大家都谙熟于心，但仍旧是每季必买的送分题。简单的袜子堆叠着穿或是翻折，便能打造不一样的效果。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPHALia9X2tQpAtsZGib6GNmEiaGJeFicghqcoJrGhFIs9JmbDdYTL6NFhSA/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPAicJibYg3ic19TP0SAx8w9QG2RtQv0MJq5x2d4atgoXY8vCEg9ZCxlXUQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPHc4LveoBKNC9JL4xqPtm0YXicyI6stZGWjwmSESSJe491ibwjVvBRFHw/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                ],
                [
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPUsb6Ym6Hia7ZbTlRuCACfORz6IukGGOXq1YffX65HVsCWMpYNP9yIPg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                ],

                [
                    ['type' => 'text', 'content' => '#花色socks，是颜色不一样的烟火'],
                ],
                [
                    ['type' => 'text', 'content' => '如果觉得纯色sock不够刺激视神经千篇一律，那些无彩色不够温暖又无趣，色彩丰富气质活泼的袜子更能让你感受到穿搭的乐趣。且如果说外套、风衣这样的大件，要选黑白灰、暗色系求稳求百搭，那何不在袜子这样不是太贵的小件上放飞你的想象力？'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPFAlgHrVLZeVqeATFOib7uFDgOS03TcnpbleWFrWJBVAxHK8pWwIpPcQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '身边不少人的第一件Pual Smith是彩色的袜子或是手机壳，比起那些昂贵又不一定好驾驭的大件，从这种负担得起又有点缀功能的配件入手，也是不错的选择。而小狐狸Kitsuné这一季出了三款袜子，尤其喜欢火箭那一款，穿上之后走路自带BGM与风。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPU45u2x72I0zOL6SRqYSKUSWgoz9l4XF12j8jvo0DVJMuRdPvUYTzibQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPpIo8CmblrPo3bZBeOQAHJN0IIpyP6kvA1yUjCIbVxfjTl15bgUIENw/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '一说到北欧就性冷淡性冷淡… 拜托，其实北欧真的不是只有冷淡。由于地理因素与气候特征，导致北欧五国其实也有很多色彩缤纷的品牌，体现了他们对温暖多彩的渴望，去填补自然环境中相对缺失的那一部分。丹麦品牌Henrik Vibskov每一季都会推出花色袜子，精心设计的图案、丰富的细节，童心满满，动人。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzP5MPV1YmH2F2GDGp47jfr7eLzcDsVPo1mEhJnSeqpCU7CbRLRaU38GQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPVYuuO0RAztuYAeafaW9z0RcJZickEnJf6ne1V6ugiaudSa7o2u3aXQ3g/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '几个月前和优衣库联名过的荷兰国宝级品牌Marimekko，缤纷的印花与图案设计是他们的拿手好戏。虽然主打家居用品与女装，但在官网袜子的List里，仍旧找到了不少男袜，包括了最经典的Unikko图案。如果觉得缤纷的亮色罂粟花、波点过于“娘气”，不妨选择活泼的图案x低调的配色方式。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPiaCfFaDZI291mRt81EYEaaZnpc2fcw8IxKpiaex7ichbBB8jibdjEl9BLw/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => 'Tyo-to-to的袜子都是足袋的款式，足袋是将拇指和其他四指分开的袜子，martin margiela 的Tabi shoes分趾鞋，灵感便的来自这种日式传统袜。金鱼、祥云、富士山、仙桃等和风向的元素常常出现其中。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPOtIWObuhvICliblLXE5fqoHM06seqwZc1Q7Xyguo8n0Vq18uLYwt6LQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                ],
                [
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPUsb6Ym6Hia7ZbTlRuCACfORz6IukGGOXq1YffX65HVsCWMpYNP9yIPg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                ],

                [
                    ['type' => 'text', 'content' => '“如果你要穿上袜子，那就压根和时尚沾不了边……不要想是为啥，反正就是别穿。” Valentino 的两位创意总监 Maria Grazia Chiuri 和 Pierpaolo Piccioli曾这样说过。'],
                ],
                [
                    ['type' => 'text', 'content' => '然而，在他们口中“反时尚”的袜子成为了如今潮流青年们“争奇斗艳”的细节。行走间“无意”露出的这一丝线索，有着装扮功能但也暗暗透露给别人我们外壳里面的“样式”。你妈多久给没给你穿袜子买袜子，你也得给自己买个袜子吧：）'],
                ],
            ],
        ];


        return $content;
    }


    /**
     * @SWG\Get(
     *     path="/site/index-two",
     *     tags={"实例"},
     *     summary="文章导入格式2",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionIndexTwo()
    {
        exit;
        $content = [
            'authorInfo'=>['author' => "Bowen", "authorIconLink" => "https://wx.qlogo.cn/mmopen/vi_32/kVroh8aKgIJRJYluTLmzPJpj7Hicsrs9iaJsyQy3pSqqntOEbYHVrH0U3tWTQ6HhPuEI6XqLFnnUGW3rfRAcT6mA/132", 'unionid' => 'BowenUnionid'],
            'articleInfo' => [
                'title' => '让陈冠希迷恋的仓老师',
                'subTitle' => '让陈冠希迷恋的仓老师',
                'summary' => '让陈冠希迷恋的仓老师',
                'headImg' => 'https://mmbiz.qpic.cn/mmbiz_png/nQEJicQRRURrx7YDzfEkiaHiate3MVIwkzPmqbASdY9zgdlkRL0mR86cdchdpGcoFaoL97xicZ7CUnqndPbUxjlgIA/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1',
                'orderId' => 10,
            ],
            'paragraph' => [
                [
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt4812pxHKVEMbZlhHjkLl9WGWrtERg3H8ClSMvpukchtFnicsyqF7VEMLg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                ],

                [
                    ['type' => 'text', 'content' => '这两年陈冠希在镜头下大多是和A$AP们相处甚欢的画面，差不多都掩盖住了曾经他与各种日本潮流ICON的密切关系。多亏一个月前的一张照片，才让我又续想起了他和那位日本设计师「眉来眼去」的故事。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48CiabFT0pWBpCw22I5hzuYiagEPOYlMG2U1hnEZic3XSvko95HkIpu93mg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '九月初陈冠希身着The North Face UE x Kazuki的系列，让「Edison」和「Kazuki」这俩元素再次合二为一。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48jaa9Jkt794Q4AP7uEc7oRd8RibWOUyhQa5tOoWC3dRkicY3LHKH8yQYQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '如果没记错的话，上一次把两人联系在一起时，我还仅仅靠的是「脑补」：去年仓石一树携手自己的A.FOUR Labs和KAPPA把复古串标玩得火热，让我联想到了15年CLOT x KAPPA时陈冠希上身自家的联名串标夹克。先后让KAPPA串标回火，成了我最近一次对他们俩的记忆。而再之前陈冠希运用CLOT以外的品牌和仓石一树联名，也远没有当年掀起的波澜大。陈冠希和仓石俩人一起鼓捣的东西没能成为爆点，这放以前怕不是一件Unbelieveable的事情...'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48uxun1qyF9bnvKEcZKkw4hwtIG3ic55XpeuXxhNXttzDIahAxEaDtQQQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '15年仓石一树在已经和陈冠希分道扬镳的YOHO!潮流展上身着「Where the fuck is Edison？」TEE，可算是为兄弟插自己两刀的典范了这个黄金组合的巅峰似乎已经过去，但也并不妨碍我们对仓石一树投以羡慕之情，毕竟这可是陈老师在团队之外少有的True Brother呢。Nigo曾说联名是一件不太有意义的事情，可同样出身BAPE的仓石一树却更加开放，不同于Nigo对待联名的严肃感，仓石一树则希望能通过与其他品牌的合作，来提升实力、拓展视野。这样积极的心态，或许就是让陈冠希和仓石组合在21世纪最初的十年走红的一个原因吧。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48sdTowkkQu6pTN2EPgLgp2QOeDCY1pxibgJc71qdx6OcuEofdWhNOqAQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '除此之外，当然也少不了过硬的设计功底。仓石一树自己表示喜欢舒适、简约的设计，但依照他的经历来讲，他对自己的评价可有点谦虚了。从早年的NoWhere和BAPE开始，到Fragment Design、Neighborhood、Visvim，再到Adidas、Levi\'s、KAPPA，你都可以看到这名「自由设计师」的身影。能完美胜任这么多品牌的设计，仓石一树所能办到的绝不仅仅是简约，于是你也不难理解为啥国内好多对仓石一树介绍的开头，要加上「鬼才」二字。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48icGrYjGb6iazY6iclY0jXFmg0qfd3GugUQa444XhtMLdw8L4pUUibHD05w/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '陈冠早在CLOT成立之初就对Visvim十分迷恋（当然那时的大多数ICON也一样）。各种报道和街拍，让我们对这个当时还是主攻鞋履的品牌有了认识。除了后来又被Kanye回火了一把的FBT外，那双「笨重的拖鞋」肯定让你记忆深刻。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48H1upCaOAG2Jl6UBDIQKTywAt1TC0tzTibBrtexbRKyPlibicUMuMyCtYw/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '在2003年推出的Christo正是由当时还在为教父效力的仓石一树主导设计。要说冠希当年有多爱它？估计用「人在鞋在」来形容都不为过吧。爱的太深刻，于是拿CLOT合作推出了联名款，于是就有了他怀抱CLOT Royale x Visvim Christo的经典海报。这么多年这个淘宝爆款依旧还能在大街上占一席之地，当初迷住冠希眼球的仓石一树可得负主要责任。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48rfddr9pn3bje5VaKDtW36ahnQFKf38Zm0FEW5yTApPMO5C5wUs2jmg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '迷恋不如直接合作，「荆棘ALIENEGRA」就是CLOT和MADSAKI还有仓石一树合作的产物，它火到俗不可耐就说明了这个系列的地位。在初代之后，脱胎于ALIENEGRA Graphic Tee的绿色荆棘迷彩堪称经典，那件拥有面具、闪粉的Sleeve Parka也成了很多人的终（gui）极（de）梦（yi）幻（bi）单品。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48W1Vic27xKehEqKEaWM26ODjgdfk3l44eu5PCCFfBGJhHVDTM0icXAj8g/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '后来CLOT还拉来了Kaws对荆棘进行升级。不过Kaws算是陈冠希崇拜的偶像，而仓石才是真正的「老铁」。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48iblaMBW0duWkQVibUffgAzZhhmd91Rp4SicfzIaNWjK4QX0fRC7XAZvEg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48ZgSdoqe2EyPN7FiboCPZNzYkzEoOdqvICTMWA6JPsIlmYZXiatZyN8vg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '在Levis和Neighborhood牛仔裤分庭抗礼的时间里，壕之潮人的裤装标配可以是一件Fenom Crush。CLOT和仓石一树合作的「金线」和「银线」虽然因为艳照门事件没有了Fenom加持，但丝毫没能阻挡大伙的热情。率先发售的「银线」Levi\'s x Clot x Kazuki “Platinum kzKLOT Levi\'s 505”,名字里kzKLOT的出现，成为了俩人基情升华的佐证。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48HbhxCPtxXUPaMG19bibN4fRsot4MuFGqOeYT2Bah7U5Z9kB4BDu6GhQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '随着这些年两人的品牌和项目更加成熟、丰富，陈冠希和仓石一树越来越多的以独立的姿态出现。不过「情怀」这个用到即使烂俗的词汇，还真是让人难以割舍——想必陈冠希和仓石一树这些年在事业上的进步，多半也受益于当年合作的黄金时期吧。'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48Wiahh5Z5KOkp21fAx2riaXGYNDtavQ4sGzYjTaQ1ibJDKBQaibahlYYSlg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '仓石一树同哥特体艺术家Cali Thornhill Dewitt建立的BEAT与陈冠希肆个仆街的4PK Labs进行联名，16年的「我怎么会变成如此一头怪兽」'],
                    ['type' => 'img', 'content' => 'https://mmbiz.qpic.cn/mmbiz_jpg/nQEJicQRRURrYOiaV9d5zcwiaUYvtQEkt48U8Gf08tzHlFicaLbBTUbJt5wrGia9FUxWPAFqIw2fmJfYJYpwAkeySyg/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1'],
                    ['type' => 'text', 'content' => '如今，在长时间日潮式微的呼声里，几家新晋设计师似乎又有将日本潮流重新拉回圆心的架势，实在让人欣喜。而借着陈老师再度上身Kazuki设计这一波，不知道大伙能否再去幻想幻想「EDC x 日本潮流」的新局面呢？'],
                ],
            ],

        ];

        return $content;
    }

    public function actionError() {

    }
}
