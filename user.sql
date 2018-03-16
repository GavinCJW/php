/*
Navicat MySQL Data Transfer

Source Server         : 10.10.78.107
Source Server Version : 50540
Source Host           : 10.10.78.107:3306
Source Database       : xcf_fund

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2018-03-16 18:32:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `user_group_id` int(11) NOT NULL COMMENT '所在用户组ID',
  `username` varchar(20) NOT NULL COMMENT '登录名',
  `password` varchar(40) NOT NULL COMMENT '密码',
  `fullname` varchar(32) NOT NULL COMMENT '用户姓名',
  `email` varchar(96) NOT NULL COMMENT 'email',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态 1正常 0屏蔽 -1注销',
  `date_added` datetime DEFAULT NULL COMMENT '创建时间',
  `dept` varchar(100) DEFAULT NULL COMMENT '所在部门',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '1', 'admin', '2e7fbf29172b07ad2da859d7636a444c', '超级用户', '', '1', '2016-12-28 10:40:31', '1');
INSERT INTO `user` VALUES ('3', '1', 'zhongsi', '81bda40748c7862a1651ed4b19ec0196', '钟思', '', '1', '2016-12-22 00:00:00', 'it');
INSERT INTO `user` VALUES ('4', '2', 'test', '81bda40748c7862a1651ed4b19ec0196', '测试账号', '', '1', '2017-01-11 00:00:00', 'it');
SET FOREIGN_KEY_CHECKS=1;
