set autocommit=0;
create database woof_admin charset=utf8;
use woof_admin;

CREATE TABLE admin (
  aid int(11) NOT NULL AUTO_INCREMENT COMMENT '管理者ID',
  username VARCHAR(64) NOT NULL COMMENT '用户名',
  password VARCHAR(255) NOT NULL COMMENT '密码',
  `type` tinyint(1) not null default 1 comment '1:正常账户 2：授权账户',
  status tinyint(1) NOT NULL DEFAULT 1 COMMENT '1:启用 0:禁用',
  cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  uTime TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  deleteFlag tinyint(1) not null DEFAULT 0 COMMENT '删除标识:0正常，1删除',
  PRIMARY KEY (aid),
  UNIQUE KEY username(username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '后台用户登录表';


create table file_info(
  `fileId` varchar(255) NOT NULL COMMENT '文件md5标识',
  `path` varchar(255) not null comment 'path地址',
  `sourceType` int(11) not null default 1 comment '资源服务器供应商， 1:又拍云',
  `url` text COMMENT 'url地址',
  `fileSize`  int(11) not null default 0 comment '文件大小',
  cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (fileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '文件信息表';

create table role(
  roleName varchar(255) not null comment '角色名称',
  cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  uTime TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  deleteFlag tinyint(1) not null DEFAULT 0 COMMENT '删除标识:0正常，1删除',
  PRIMARY KEY (roleName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '后台角色表';

create table role_admin(
  roleName varchar(255) not null comment '角色名称',
  aid int(11) NOT NULL COMMENT '管理者ID',
  PRIMARY KEY (roleName, aid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '后台角色-后台用户映射表';

create table operation_article(
  aid int(11) NOT NULL COMMENT '管理者ID',
  articleId int(11) NOT NULL COMMENT '文章ID',
  cTime TIMESTAMP not null  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  uTime TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (aid, articleId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '后台操作员-文章映射表';