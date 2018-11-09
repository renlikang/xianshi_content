set autocommit=0;
create database woof_content charset=utf8mb4;
use woof_content;

CREATE TABLE `article` (
  `articleId` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章ID',
  `authorId` int(11) NOT NULL COMMENT '作者ID',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '文章类型，1 PGC 2 爬虫 3 UGC',
  `source` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '文章来源',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `subTitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '副标题',
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '内容摘要',
  `headImg` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头图',
  `content` json DEFAULT NULL COMMENT '文章内容',
  `orderId` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1:启用 0:禁用',
  `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `uTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleteFlag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识:0正常，1删除',
  PRIMARY KEY (`articleId`),
  KEY `authorId` (`authorId`)
) ENGINE=InnoDB AUTO_INCREMENT=3598 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章表';


CREATE TABLE article_comment(
  commentId int(11) NOT NULL AUTO_INCREMENT COMMENT '评论ID',
  articleId int(11) NOT NULL COMMENT '内容ID',
  parentId int(11) NOT NULL DEFAULT 0 COMMENT '父ID',
  uid int(11) NOT NULL DEFAULT 0 COMMENT '用户ID',
  `content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '评论内容',
  cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  uTime TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  deleteFlag tinyint(1) not null DEFAULT 0 COMMENT '删除标识:0正常，1删除',
  PRIMARY KEY (commentId),
  INDEX articleId(articleId),
  INDEX uid(uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT '评论表';

CREATE TABLE article_praise (
  praiseId int(11) NOT NULL AUTO_INCREMENT COMMENT '点赞ID',
  articleId int(11) NOT NULL COMMENT '内容ID',
  uid int(11) NOT NULL DEFAULT 0 COMMENT '用户ID',
  cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  uTime TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  deleteFlag tinyint(1) not null DEFAULT 0 COMMENT '删除标识:0正常，1删除',
  PRIMARY KEY (praiseId),
  INDEX articleId(articleId),
  INDEX uid(uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT '点赞表';


CREATE TABLE author_attention (
  authorId int(11) NOT NULL COMMENT '作者ID',
  uid int(11) NOT NULL COMMENT '用户ID',
  cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  uTime TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  deleteFlag tinyint(1) not null DEFAULT 0 COMMENT '删除标识:0正常，1删除',
  PRIMARY KEY (authorId, uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT '关注表';

CREATE TABLE `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息Id',
  `type` set('like','comment','follow','notice') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '消息类型',
  `content` json NOT NULL COMMENT '消息内容',
  `is_read` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已读，0未读，1已读',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除，0未删除，1已删除',
  `create_at` int(11) NOT NULL COMMENT '创建时间',
  `update_at` int(11) NOT NULL COMMENT '更新时间',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '通知的用户Id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息记录表';

CREATE TABLE `article_forward` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '转发Id',
  `articleId` int(11) NOT NULL COMMENT '文章Id',
  `uid` int(11) NOT NULL COMMENT '用户Id',
  `cTime` int(11) NOT NULL COMMENT '创建时间',
  `uTime` int(11) NOT NULL COMMENT '更新时间',
  `deleteFlg` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标识：0正常，1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='转发表';


create table `tag_map` (
  `tagName` varbinary(255) COMMENT '标签名称',
  `mapId` int(11) not null comment '映射ID',
  `mapType` tinyint(1) not null default  1 comment '类型 1：文章 2：用户的标签 3：用户喜欢的话题',
  PRIMARY KEY (`tagName`, `mapId`, `mapType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='标签映射表';

create table `tag` (
  `tagName` varbinary(255) COMMENT '标签名称',
  `headImg` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '头图',
  `realTagName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标签名称（real）',
  `md5TagName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标签名称（md5）',
  PRIMARY KEY (`tagName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='标签表';



