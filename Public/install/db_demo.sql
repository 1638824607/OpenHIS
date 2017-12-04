/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50554
Source Host           : localhost:3306
Source Database       : web04

Target Server Type    : MYSQL
Target Server Version : 50554
File Encoding         : 65001

Date: 2017-11-30 10:05:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dzm_his_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_auth_group`;
CREATE TABLE `dzm_his_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '' COMMENT '组别名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示：1为显示，2不显示',
  `is_manage` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1需要验证权限 2 不需要验证权限',
  `rules` text NOT NULL COMMENT '用户组拥有的规则id， 多个规则',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='用户组表';

-- ----------------------------
-- Records of dzm_his_auth_group
-- ----------------------------
INSERT INTO `dzm_his_auth_group` VALUES ('1', '管理员', '1', '1', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,84,85,86,89,87,88,141,142,143,144,145,139,148,149');
INSERT INTO `dzm_his_auth_group` VALUES ('2', '医生', '1', '1', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,61,62,63,64,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,90,91,92,93,96,98,99,100,102,103,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,84,85,86,87,88,89,141,142,143,144,147,148,149,150,151,152,153,154,155,156,157,158,159,160');
INSERT INTO `dzm_his_auth_group` VALUES ('3', '护士', '1', '1', '1,2,3,4');
INSERT INTO `dzm_his_auth_group` VALUES ('4', '挂号员', '1', '1', '1,2,3,4,5');
INSERT INTO `dzm_his_auth_group` VALUES ('5', '收费员', '1', '1', '5,6,7,1,2,3,4,5,8');
INSERT INTO `dzm_his_auth_group` VALUES ('6', '发药员', '1', '1', '1,2,3,4,5');
INSERT INTO `dzm_his_auth_group` VALUES ('7', '财务', '1', '1', '1,2,3,4,5');
INSERT INTO `dzm_his_auth_group` VALUES ('8', '其他人员', '1', '1', '4');

-- ----------------------------
-- Table structure for dzm_his_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_auth_group_access`;
CREATE TABLE `dzm_his_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT '用户表member-ID，',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='用户组与用户关联表';

-- ----------------------------
-- Records of dzm_his_auth_group_access
-- ----------------------------
INSERT INTO `dzm_his_auth_group_access` VALUES ('1', '1');
INSERT INTO `dzm_his_auth_group_access` VALUES ('2', '2');

-- ----------------------------
-- Table structure for dzm_his_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_auth_rule`;
CREATE TABLE `dzm_his_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `icon` varchar(100) DEFAULT '' COMMENT '图标',
  `menu_name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则唯一标识Controller/action',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `pid` tinyint(5) NOT NULL DEFAULT '0' COMMENT '菜单ID ',
  `is_menu` tinyint(1) DEFAULT '1' COMMENT '1:是主菜单 2 否',
  `is_race_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:是 2:不是',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  `order_list` int(255) DEFAULT '0' COMMENT '排序字段',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4 COMMENT='用户权限表';

-- ----------------------------
-- Records of dzm_his_auth_rule
-- ----------------------------
INSERT INTO `dzm_his_auth_rule` VALUES ('1', '', 'Index/base_index', '看病就诊', '0', '1', '1', '1', '1', '', '1');
INSERT INTO `dzm_his_auth_rule` VALUES ('2', 'fa fa-medkit', 'Doctor/index', '新开就诊', '1', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('3', 'fa fa-list', 'Doctor/getVisitList', '就诊列表', '1', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('4', '', 'Doctor/getMedicines', '获取药品', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('5', 'fa fa-calendar', 'Scheduling/Scheduling_list', '医生排班', '1', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('6', 'fa fa-stethoscope', 'Registration/Registration_add', '门诊挂号', '1', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('7', 'fa fa-list-alt', 'Registration/Registration_list', '挂号列表', '1', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('8', 'fa fa-user', 'Patient/index', '患者库', '1', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('9', '', '', '编辑医生排班', '5', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('10', '', '', '收费发药', '0', '1', '1', '1', '1', '', '2');
INSERT INTO `dzm_his_auth_rule` VALUES ('11', 'fa fa-jpy', 'Doctor/pkgList', '收费发药', '10', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('12', 'fa fa-minus-square', 'Doctor/pkgRefund', '处方退费', '10', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('13', '', 'Doctor/pkgRefundDo', '退费操作', '12', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('14', '', 'Doctor/pkgPay', '收费操作', '11', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('15', '', 'Doctor/pkgShow', '订单明细', '11', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('16', '', '', '数据统计', '0', '1', '1', '1', '1', '', '4');
INSERT INTO `dzm_his_auth_rule` VALUES ('17', 'fa fa-pie-chart', 'IncomeStat/index', '诊所收支统计', '16', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('18', 'fa fa-suitcase', 'DrugSalesStatistics/index', '药品销售统计', '16', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('19', 'fa fa-bar-chart', 'Inspectionfee/inspectionStatistics', '检查项目统计', '16', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('20', 'fa fa-signal', 'ReportStatistics/monthlyReport', '年月报表统计', '16', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('21', 'fa fa-calculator', 'WorkloadStatistics/index', '工作量统计', '16', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('22', '', '', '系统设置', '0', '1', '1', '1', '1', '', '5');
INSERT INTO `dzm_his_auth_rule` VALUES ('23', 'fa fa-building-o', 'Member/userList', '医生管理', '22', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('24', '', 'Member/resetPassword', '重置密码', '23', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('25', 'fa fa-drivers-license', 'Department/index', '科室管理', '22', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('26', 'fa fa-id-badge', 'Registeredfee/Registeredfee_list', '挂号费管理', '22', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('27', 'fa fa-money', 'PrescriptionExtracharges/index', '处方附加费', '22', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('28', 'fa fa-sheqel', 'Inspectionfee/index', '检查项目费', '22', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('29', 'fa fa-book', 'Dictionary/index', '字典表维护', '22', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('30', '', 'AuthGroup/ruleGroup', '查看职务权限', '23', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('31', '', 'Doctor/getExtracharges', '获取附加费用', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('32', '', '', '药品进销存', '0', '1', '1', '1', '1', '', '3');
INSERT INTO `dzm_his_auth_rule` VALUES ('33', 'fa fa-medkit', 'Medicines/index', '药品信息维护', '32', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('34', 'fa fa-ambulance', 'Supplier/index', '供应商维护', '32', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('35', '', 'Doctor/getInspectionfee', '获取检查项目费用', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('36', '', 'Doctor/getRegistrations', '获取挂号列表', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('37', '', 'Doctor/searchPatientByMobile', '用手机号获取用户信息', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('38', '', 'Doctor/getUserInfo', '获取患者档案', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('39', '', 'Doctor/saveCareInfo', '保存患者历史病历', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('40', '', 'Doctor/getCareHistory', '获取患者历史病历', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('41', '', 'Doctor/getPatientList', '获取患者列表', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('42', '', 'Doctor/savePatient', '保存患者信息', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('43', '', 'Doctor/getPkgList', '获取看病记录', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('44', '', 'Doctor/saveOrder', '保存', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('45', '', 'Doctor/change_ol_pay_part', '更新在线支付额度', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('46', '', 'Doctor/payOrder', '支付订单', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('47', '', 'Doctor/getOnLinePay', '获取在线支付', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('48', '', 'Doctor/getOrder', '显示收费列表', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('49', 'fa fa-shopping-cart', 'Inventory/purchase', '采购入库', '32', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('50', 'fa fa-check', 'BatchesOfInventory/get_list', '入库审核', '32', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('51', 'fa fa-search', 'Inventory/inventory_list', '库存查询', '32', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('52', '', 'Supplier/index', '备用', '32', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('53', '', 'Supplier/index', '备用', '32', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('54', '', 'Supplier/index', '备用', '32', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('55', '', 'Supplier/index', '备用', '32', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('56', '', 'Member/RemoveUserList', '禁用医生列表管理', '23', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('57', '', 'Member/removeUser', '移除医生', '23', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('58', '', 'Member/startUser', '取消禁用', '23', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('59', '', 'Member/addUser', '添加医生', '23', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('60', '', 'Member/editUser', '编辑医生', '23', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('61', '', 'Member/uploadDocPic', '编辑医生图片', '23', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('63', '', 'Doctor/getCareOrder', '显示看诊列表', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('64', '', 'Doctor/getCareOrderSub', '显示看诊列表明细', '2', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('65', 'fa fa-info-circle', 'Member/myHospitalInfo', '诊所信息', '22', '1', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('66', '', 'Doctor/pkgDone', '完成交易', '11', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('67', '', 'Doctor/pkgRefundDo', '执行退款', '11', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('68', '', 'Doctor/getRefundLog', '查看退款ajax', '11', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('69', '', 'Doctor/pkgIO', '交易信息', '11', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('70', '', 'Patient/editPatient', '编辑患者档案', '8', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('71', '', 'Patient/removePatient', '移除患者', '8', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('72', '', 'Patient/removedLists', '移除患者列表', '8', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('73', '', 'Patient/recoveryPatient', '恢复患者', '8', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('74', '', 'Patient/deletePatient', '删除患者', '8', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('75', '', 'Patient/careHistory', '历史病例', '8', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('76', '', 'Patient/exportExcel', '导出患者信息', '8', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('77', '', 'ReportStatistics/exportMonthlyReport', '月度报表导出', '20', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('78', '', 'ReportStatistics/monthlyReport', '月度报表', '20', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('79', '', 'ReportStatistics/yearReport', '年度报表', '20', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('80', '', 'ReportStatistics/exportYearReport', '年度报表导出', '20', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('81', '', 'Department/addDepartment', '添加科室', '25', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('82', '', 'Department/editDepartment', '编辑科室', '22', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('83', '', 'Department/deleteDepartment', '删除科室', '22', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('84', '', 'PrescriptionExtracharges/addExtraCharges', '添加处方附加费', '27', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('85', '', 'PrescriptionExtracharges/editExtraCharges', '修改处方附加费', '27', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('86', '', 'PrescriptionExtracharges/deleteExtraCharges', '删除处方附加费', '27', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('87', '', 'Inspectionfee/addInspection', '添加检查项目费', '28', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('88', '', 'Inspectionfee/editInspection', '编辑检查项目费', '28', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('89', '', 'Inspectionfee/deleteInspection', '删除检查项目费', '28', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('90', '', 'Dictionary/dictionaryLists', '字典列表', '29', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('91', '', 'Dictionary/getSubDictionary', '字典子列表', '29', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('92', '', 'Dictionary/addDictionary', '添加字典', '29', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('93', '', 'Dictionary/editDictionary', '编辑字典', '29', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('94', '', 'Dictionary/deleteDictionary', '删除字典', '29', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('95', '', 'Registeredfee/Registeredfee_edit', '挂号费用编辑', '26', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('96', '', 'Medicines/medicinesLists', '全部药品列表', '33', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('97', '', 'Registeredfee/Registeredfee_add', '挂号费用添加', '26', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('98', '', 'Medicines/addMedicines', '添加药品', '33', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('99', '', 'Registeredfee/getRegisteredfeeInfoByReg_id', '挂号费用弹框赋值', '26', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('100', '', 'Medicines/deleteMedicines', '删除药品', '33', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('101', '', 'Registeredfee/Registeredfee_delete', '挂号费用删除', '26', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('102', '', 'Supplier/addSupplier', '添加供应商', '34', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('103', '', 'Supplier/editSupplier', '编辑供应商', '34', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('104', '', 'Registeredfee/getRegisteredFeeList', '获取挂号费用列表', '26', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('105', '', 'Supplier/deleteSupplier', '删除供应商', '34', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('106', '', 'Registration/getSchedulingList', '门诊挂号获取医生排班信息', '6', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('107', '', 'Registration/change_ol_pay_part', '门诊挂号更新在线支付额度', '6', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('108', '', 'Registration/getOnLinePay', '门诊挂号获取在线支付状态', '6', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('109', '', 'Registration/payOrder', '门诊挂号保存订单', '6', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('110', '', 'Registration/ForAge', '门诊挂号获取年龄', '6', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('111', '', 'Registration/getPatientPool', '门诊挂号获取患者列表', '6', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('112', '', 'Registration/getPatientInfo', '门诊挂号选中患者', '6', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('113', '', 'Registration/getPaylogInfo', '诊所列表获取paylog信息', '7', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('114', '', 'Registration/Registration_quit', '诊所列表更改挂号状态', '7', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('115', '', 'Registration/Registration_cancel', '诊所列表作废', '7', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('116', '', 'Scheduling/Scheduling_edit', '排班弹框', '5', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('117', '', 'Scheduling/Scheduling_change', '更改排班状态', '5', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('118', '', 'Scheduling/export', '排班导出', '5', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('119', '', 'Inventory/getMedicinesList', '采购入库获取药品信息', '49', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('120', '', 'Inventory/submitMedicines', '采购入库添加药品', '49', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('121', '', 'Inventory/submit_purchasing_information', '采购入库添加', '49', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('122', '', 'Inventory/adjust_price', '库存查询 调价', '51', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('123', '', 'Inventory/getInventoryListInfo', '库存查询 获取药品库存列表', '51', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('124', '', 'Inventory/getBatchesOfInventoryListStatusEqTwo', '库存查询 批次库存及价格', '51', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('125', '', 'BatchesOfInventory/delete_batches_of_inventory', '入库审核 删除', '50', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('126', '', 'BatchesOfInventory/getBatchesOfInventoryList', '入库审核 获取审核列表', '50', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('127', '', 'BatchesOfInventory/purchase_list', '入库审核 采购信息列表', '50', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('128', '', 'BatchesOfInventory/delete_purchase', '入库审核 删除采购信息', '50', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('129', '', 'BatchesOfInventory/get_purchase_list', '入库审核 获取采购信息列表', '50', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('130', '', 'BatchesOfInventory/audit', '入库审核 审核', '50', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('131', '', 'DrugSalesStatistics/detailList', '药品销售统计 获取明细列表', '18', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('132', '', 'IncomeStat/getIncomeInfo', '诊所收支统计 获取统计信息', '17', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('133', '', 'IncomeStat/getIncomeList', '诊所收支统计 获取列表信息', '17', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('134', '', 'IncomeStat/export', '诊所收支统计 导出', '17', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('135', '', 'WorkloadStatistics/getClinicFee', '工作量统计 门诊费用统计', '21', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('136', '', 'WorkloadStatistics/getDrugPurchase', '工作量统计 获取挂号费统计', '21', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('137', '', 'WorkloadStatistics/getCareOrderStatistics', '工作量统计 门诊处方统计', '21', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('138', '', 'WorkloadStatistics/getCollectionStatistics', '工作量统计 收费员统计', '21', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('139', '', 'WorkloadStatistics/ClinicFee_export', '工作量统计 门诊费用导出', '21', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('140', '', 'WorkloadStatistics/DrugPurchase_export', '工作量统计 挂号费用导出', '21', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('141', '', 'Registration/registrationRefund', '挂号列表 退号退款', '7', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('142', '', 'Registration/pkgRefundDo', '挂号列表 去退款', '7', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('143', '', 'Registration/registrationGoToPay', '挂号列表 去支付', '7', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('144', '', 'Registration/getRegistrationPayInfo', '挂号列表 获取详细信息', '7', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('145', '', 'Registeredfee/getRepetition', '挂号费用查询重复', '26', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('147', '', 'Doctor/printOrder', '打印处方', '11', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('148', '', 'Inventory/set_early_warning', '库存查询  设置预警', '51', '2', '1', '1', '1', '', '0');
INSERT INTO `dzm_his_auth_rule` VALUES ('149', 'fa fa-cog', 'HisWxmp/index', '第三方配置', '22', '1', '1', '1', '1', '', '0');

-- ----------------------------
-- Table structure for dzm_his_batches_of_inventory
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_batches_of_inventory`;
CREATE TABLE `dzm_his_batches_of_inventory` (
  `batches_of_inventory_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '批次库存ID',
  `company_id` int(10) NOT NULL COMMENT '诊所ID',
  `supplier_id` int(10) NOT NULL COMMENT '供应商ID',
  `batches_of_inventory_number` bigint(20) NOT NULL COMMENT '采购单编号',
  `purchasing_agent_id` int(10) NOT NULL COMMENT '采购员ID',
  `batches_of_inventory_total_money` decimal(10,2) NOT NULL COMMENT '采购总金额',
  `batches_of_inventory_date` varchar(20) NOT NULL COMMENT '制单日期',
  `batches_of_inventory_status` int(3) NOT NULL DEFAULT '1' COMMENT '审核标记；未审核：1，已审核：2',
  `batches_of_inventory_verifier` int(10) DEFAULT NULL COMMENT '审核人员ID',
  `batches_of_inventory_verifier_date` varchar(20) DEFAULT NULL COMMENT '审核日期',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`batches_of_inventory_id`),
  KEY `company_id` (`company_id`) USING BTREE,
  KEY `supplier_id` (`supplier_id`) USING BTREE,
  KEY `purchasing_agent_id` (`purchasing_agent_id`) USING BTREE,
  KEY `batches_of_inventory_verifier` (`batches_of_inventory_verifier`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='批次库存表';

-- ----------------------------
-- Records of dzm_his_batches_of_inventory
-- ----------------------------
INSERT INTO `dzm_his_batches_of_inventory` VALUES ('1', '1', '1', '201711290000100001', '1', '4499550.00', '2017-11-08', '2', '1', '1511949342', '1511949342', '1511949342');
INSERT INTO `dzm_his_batches_of_inventory` VALUES ('2', '1', '1', '201711290000100002', '1', '4499550.00', '2017-11-15', '1', null, null, '1511949357', null);

-- ----------------------------
-- Table structure for dzm_his_care_history
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_care_history`;
CREATE TABLE `dzm_his_care_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hospital_id` int(10) unsigned DEFAULT '0' COMMENT '医院id',
  `doctor_id` int(10) unsigned DEFAULT '0' COMMENT '医生id',
  `patient_id` int(10) unsigned DEFAULT '0' COMMENT '患者id',
  `department_id` int(10) unsigned DEFAULT '0' COMMENT '科室id',
  `type_id` tinyint(1) unsigned DEFAULT '0' COMMENT '接诊类型：0初诊，1复诊，2急诊',
  `is_contagious` tinyint(1) unsigned DEFAULT '0' COMMENT '是否传染，0否，1是',
  `case_date` date DEFAULT NULL COMMENT '发病日期',
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '插入时间，php时间戳',
  `case_code` varchar(32) DEFAULT NULL COMMENT '诊断编号',
  `case_title` varchar(255) DEFAULT NULL COMMENT '主诉',
  `case_result` text COMMENT '诊断信息',
  `doctor_tips` text COMMENT '医生建议',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `hospital_id` (`hospital_id`) USING BTREE,
  KEY `doctor_id` (`doctor_id`) USING BTREE,
  KEY `patient_id` (`patient_id`) USING BTREE,
  KEY `type_id` (`type_id`) USING BTREE,
  KEY `is_contagious` (`is_contagious`) USING BTREE,
  KEY `case_date` (`case_date`) USING BTREE,
  KEY `case_code` (`case_code`) USING BTREE,
  KEY `addtime` (`addtime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='历史病历';

-- ----------------------------
-- Records of dzm_his_care_history
-- ----------------------------
INSERT INTO `dzm_his_care_history` VALUES ('1', '1', '2', '2', '1', '0', '0', '2017-11-29', '1511951249', '201711291827290010020041', '没事就是来测试的', '测试数据', '测试数据', '测试数据');

-- ----------------------------
-- Table structure for dzm_his_care_order
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_care_order`;
CREATE TABLE `dzm_his_care_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hospital_id` int(10) unsigned DEFAULT '0' COMMENT '医院id',
  `doctor_id` int(10) unsigned DEFAULT '0' COMMENT '医生id',
  `patient_id` int(10) unsigned DEFAULT '0' COMMENT '患者id',
  `care_history_id` int(10) unsigned DEFAULT '0' COMMENT '关联病历id',
  `pkg_id` int(10) unsigned DEFAULT '0' COMMENT '收费总表care_pkg.id',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态，0未支付，1已支付，2确认收款，3申请退款，4已退款',
  `label` varchar(128) DEFAULT NULL COMMENT '处方名',
  `num_a` smallint(5) unsigned DEFAULT '1' COMMENT '每set_num_a天,set_num_b剂，服用set_num_c天，共set_num_d剂',
  `num_b` smallint(5) unsigned DEFAULT '1' COMMENT '每set_num_a天,set_num_b剂，服用set_num_c天，共set_num_d剂',
  `num_c` smallint(5) DEFAULT '1' COMMENT '每set_num_a天,set_num_b剂，服用set_num_c天，共set_num_d剂',
  `num_d` smallint(5) DEFAULT '1' COMMENT '每num_a天,num_b剂，服用num_c天，共num_d剂',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '单处方金额',
  `all_amount` decimal(10,2) DEFAULT '0.00' COMMENT '处方全额',
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '插入时间，php时间戳',
  `case_code` varchar(32) DEFAULT NULL COMMENT '诊断编号',
  `use_tips` text COMMENT '服药要求',
  `memo` text COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `hospital_id` (`hospital_id`) USING BTREE,
  KEY `doctor_id` (`doctor_id`) USING BTREE,
  KEY `patient_id` (`patient_id`) USING BTREE,
  KEY `addtime` (`addtime`) USING BTREE,
  KEY `case_code` (`case_code`) USING BTREE,
  KEY `dzm_his_care_order_care_history_id_index` (`care_history_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `pkg_id` (`pkg_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='处方列表';

-- ----------------------------
-- Records of dzm_his_care_order
-- ----------------------------
INSERT INTO `dzm_his_care_order` VALUES ('1', '1', '2', '2', '1', '2', '1', '处方1', '1', '1', '1', '1', '10.00', '104.00', '1511951249', '201711291827290110120145', '测试数据', '测试数据');
INSERT INTO `dzm_his_care_order` VALUES ('2', '1', '2', '2', '1', '2', '1', '处方2', '1', '1', '1', '1', '105.00', '105.00', '1511951249', '201711291827290110120179', '测试数据', '测试数据');

-- ----------------------------
-- Table structure for dzm_his_care_order_sub
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_care_order_sub`;
CREATE TABLE `dzm_his_care_order_sub` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pkg_id` int(10) unsigned DEFAULT '0',
  `fid` int(10) unsigned DEFAULT '0' COMMENT '所属开诊id',
  `type_id` tinyint(1) unsigned DEFAULT '0' COMMENT '分类：0药，1附加费，2检查项目',
  `goods_id` int(10) unsigned DEFAULT '0' COMMENT '商品id，药品id',
  `goods_name` varchar(255) DEFAULT '' COMMENT '药品名',
  `single` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '单剂量',
  `unit` varchar(20) DEFAULT '' COMMENT '单位',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '单价',
  `num` decimal(10,2) DEFAULT '0.00' COMMENT '用量',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
  `tips` varchar(255) DEFAULT NULL COMMENT '特殊要求，备注',
  `listorder` int(10) unsigned DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `fid` (`fid`) USING BTREE,
  KEY `listorder` (`listorder`) USING BTREE,
  KEY `goods_id` (`goods_id`) USING BTREE,
  KEY `type_id` (`type_id`) USING BTREE,
  KEY `pkg_id` (`pkg_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='开诊用药明细';

-- ----------------------------
-- Records of dzm_his_care_order_sub
-- ----------------------------
INSERT INTO `dzm_his_care_order_sub` VALUES ('1', '2', '1', '0', '12', '百合', '1.00', '', '47.00', '1.00', '47.00', '测试数据', '0');
INSERT INTO `dzm_his_care_order_sub` VALUES ('2', '2', '1', '0', '13', '柏子仁', '1.00', '', '47.00', '1.00', '47.00', '测试数据', '0');
INSERT INTO `dzm_his_care_order_sub` VALUES ('3', '2', '1', '1', '1', '煎药', '1.00', '1次', '10.00', '1.00', '10.00', '', '0');
INSERT INTO `dzm_his_care_order_sub` VALUES ('4', '2', '2', '2', '1', 'X光', '1.00', '次', '20.00', '1.00', '20.00', '拍片类', '0');
INSERT INTO `dzm_his_care_order_sub` VALUES ('5', '2', '2', '2', '2', 'CT', '1.00', '次', '50.00', '1.00', '50.00', '拍片类', '0');
INSERT INTO `dzm_his_care_order_sub` VALUES ('6', '2', '2', '2', '3', '血常规', '1.00', '次', '5.00', '1.00', '5.00', '化验类', '0');
INSERT INTO `dzm_his_care_order_sub` VALUES ('7', '2', '2', '2', '4', '乙肝五项', '1.00', '次', '30.00', '1.00', '30.00', '化验类', '0');

-- ----------------------------
-- Table structure for dzm_his_care_paylog
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_care_paylog`;
CREATE TABLE `dzm_his_care_paylog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pkg_id` int(10) unsigned DEFAULT NULL COMMENT 'care_pkg.id',
  `platform_code` varchar(128) DEFAULT NULL COMMENT '支付平台交易单号',
  `payment_platform` smallint(5) unsigned DEFAULT '0' COMMENT '支付方式：0现金，1微信，2支付宝，3，4，5....',
  `pay_amount` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态，0未支付，1已支付',
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `pkg_id` (`pkg_id`) USING BTREE,
  KEY `platform_code` (`platform_code`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='支付收费记录';

-- ----------------------------
-- Records of dzm_his_care_paylog
-- ----------------------------
INSERT INTO `dzm_his_care_paylog` VALUES ('1', '1', '现金', '0', '5.00', '1', '1511950110');
INSERT INTO `dzm_his_care_paylog` VALUES ('2', '2', '现金', '0', '209.00', '1', '1511951254');

-- ----------------------------
-- Table structure for dzm_his_care_pkg
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_care_pkg`;
CREATE TABLE `dzm_his_care_pkg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hospital_id` int(10) unsigned DEFAULT '0',
  `doctor_id` int(10) unsigned DEFAULT '0',
  `patient_id` int(10) unsigned DEFAULT '0',
  `care_history_id` int(10) unsigned DEFAULT '0',
  `registration_id` int(10) unsigned DEFAULT '0' COMMENT '挂号ID',
  `order_code` varchar(64) DEFAULT NULL COMMENT '商户订单号',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '应付金额',
  `ol_pay_part` decimal(10,2) DEFAULT '0.00' COMMENT '在线支付部分',
  `type_id` tinyint(1) unsigned DEFAULT '0' COMMENT '收费类型：0就诊处，1挂号处，2问答，3...',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态:0未支付，1已支付，2确认收款，3申请退款，4已退款,5部分支付,6完成交易（如：已发药），7部分退款',
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '插入时间',
  `op_place` tinyint(1) unsigned DEFAULT '0' COMMENT '操作地点：1售药，2查检项目，3附加费用，4挂号，，，，',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code` (`order_code`) USING BTREE,
  KEY `hospital_id` (`hospital_id`) USING BTREE,
  KEY `doctor_id` (`doctor_id`) USING BTREE,
  KEY `patient_id` (`patient_id`) USING BTREE,
  KEY `care_history_id` (`care_history_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `type_id` (`type_id`) USING BTREE,
  KEY `addtime` (`addtime`) USING BTREE,
  KEY `op_place` (`op_place`),
  KEY `registration_id` (`registration_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='收费总表';

-- ----------------------------
-- Records of dzm_his_care_pkg
-- ----------------------------
INSERT INTO `dzm_his_care_pkg` VALUES ('1', '1', '1', '1', '0', '0', '201711290000100001327', '5.00', '0.00', '1', '1', '1511950076', '4');
INSERT INTO `dzm_his_care_pkg` VALUES ('2', '1', '2', '2', '1', '0', '201711291827330310320392', '209.00', '0.00', '0', '1', '1511951249', '0');
INSERT INTO `dzm_his_care_pkg` VALUES ('3', '1', '1', '3', '0', '0', '201711300000100002994', '6.00', '6.00', '1', '0', '1512004052', '4');

-- ----------------------------
-- Table structure for dzm_his_care_refundlog
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_care_refundlog`;
CREATE TABLE `dzm_his_care_refundlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pkg_id` int(10) unsigned DEFAULT NULL COMMENT 'care_pkg.id',
  `order_id` int(10) unsigned DEFAULT '0' COMMENT 'his_care_order.id',
  `platform_code` varchar(128) DEFAULT NULL COMMENT '支付平台交易单号',
  `payment_platform` smallint(5) unsigned DEFAULT '0' COMMENT '支付方式：0现金，1微信，2支付宝，3，4，5....',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态，0失败，1成功',
  `addtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `adm_uid` int(10) unsigned DEFAULT '0' COMMENT '处理人id',
  `adm_ip` varchar(32) DEFAULT NULL COMMENT '处理人ip',
  `adm_memo` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `pkg_id` (`pkg_id`) USING BTREE,
  KEY `platform_code` (`platform_code`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `adm_uid` (`adm_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退款记录';

-- ----------------------------
-- Records of dzm_his_care_refundlog
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_cash_out
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_cash_out`;
CREATE TABLE `dzm_his_cash_out` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hospital_id` int(10) unsigned DEFAULT '0' COMMENT '所属医院id',
  `appid` varchar(32) DEFAULT NULL COMMENT '微信appid',
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '申请人id',
  `openid` varchar(32) DEFAULT NULL COMMENT '提现人openid',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '提现金额',
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '申请时间',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '申请状态：0待审核，1已通过，2已驳回',
  `ip` varchar(32) DEFAULT NULL COMMENT '申请ip',
  `type_id` tinyint(1) unsigned DEFAULT '0' COMMENT '提现接收方式：0微信，1支付宝，2银行，3现金',
  `type_fix` varchar(255) DEFAULT NULL COMMENT '如果不是微信，则填写自己的收款方式',
  `adm_uid` int(10) unsigned DEFAULT '0' COMMENT '处理人id',
  `adm_ip` varchar(32) DEFAULT NULL COMMENT '处理人ip',
  `adm_memo` varchar(255) DEFAULT NULL COMMENT '处理人意见',
  `adm_time` int(10) unsigned DEFAULT '0' COMMENT '处理时间',
  PRIMARY KEY (`id`),
  KEY `hospital_id` (`hospital_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `type_id` (`type_id`),
  KEY `adm_uid` (`adm_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户提现申请';

-- ----------------------------
-- Records of dzm_his_cash_out
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_demo_doctor
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_demo_doctor`;
CREATE TABLE `dzm_his_demo_doctor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `truename` varchar(32) DEFAULT NULL COMMENT '医生姓名',
  `dtitle` varchar(32) DEFAULT NULL COMMENT '医生职称',
  `sex` varchar(10) DEFAULT NULL COMMENT '性别',
  `address` varchar(255) DEFAULT NULL,
  `hospital_name` varchar(255) DEFAULT NULL COMMENT '医院姓名',
  `pricea` varchar(32) DEFAULT NULL COMMENT '面诊费用价格',
  `priceb` varchar(32) DEFAULT NULL COMMENT '电话问诊费用价格',
  `hasfix` varchar(255) DEFAULT NULL COMMENT '擅长',
  `about` text COMMENT '关于',
  `case` varchar(255) DEFAULT NULL COMMENT '简介',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公示信息--医生';

-- ----------------------------
-- Records of dzm_his_demo_doctor
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_demo_patient
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_demo_patient`;
CREATE TABLE `dzm_his_demo_patient` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pname` varchar(32) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众数据--患者';

-- ----------------------------
-- Records of dzm_his_demo_patient
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_department
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_department`;
CREATE TABLE `dzm_his_department` (
  `did` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '科室id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `department_name` varchar(50) NOT NULL COMMENT '科室名称',
  `department_number` varchar(50) NOT NULL COMMENT '科室编号',
  `hid` int(10) NOT NULL COMMENT '医院id',
  PRIMARY KEY (`did`),
  KEY `editdate` (`update_time`) USING BTREE,
  KEY `department_name` (`department_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='科室表';

-- ----------------------------
-- Records of dzm_his_department
-- ----------------------------
INSERT INTO `dzm_his_department` VALUES ('1', '1511948695', '0', '儿科', '2017112900000001', '1');
INSERT INTO `dzm_his_department` VALUES ('2', '1511948713', '0', '牙科', '2017112900000002', '1');
INSERT INTO `dzm_his_department` VALUES ('3', '1511948720', '0', '妇科', '2017112900000003', '1');

-- ----------------------------
-- Table structure for dzm_his_dictionary
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_dictionary`;
CREATE TABLE `dzm_his_dictionary` (
  `did` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '字典字段id',
  `dictionary_name` varchar(50) NOT NULL DEFAULT '' COMMENT '字典名称',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '父级id',
  `hid` int(10) NOT NULL DEFAULT '0' COMMENT '医院id',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '类型  0：系统  1：自建',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `number` varchar(255) NOT NULL DEFAULT '' COMMENT '诊断编号',
  `is_del` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`did`),
  KEY `parent_id` (`parent_id`),
  KEY `hid` (`hid`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='字典表';

-- ----------------------------
-- Records of dzm_his_dictionary
-- ----------------------------
INSERT INTO `dzm_his_dictionary` VALUES ('1', '药品信息', '0', '0', '0', '1508983676', '0', '', '1');
INSERT INTO `dzm_his_dictionary` VALUES ('2', '处方信息', '0', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('3', '检查项目', '0', '0', '0', '1508983676', '0', '', '1');
INSERT INTO `dzm_his_dictionary` VALUES ('4', '人员信息', '0', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('5', '生产厂家', '1', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('6', '西药用法', '1', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('7', '中药用法', '1', '0', '0', '1508983676', '0', '', '1');
INSERT INTO `dzm_his_dictionary` VALUES ('8', '发票项目', '1', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('9', '西药备注', '1', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('10', '中药备注', '1', '0', '0', '1508983676', '0', '', '1');
INSERT INTO `dzm_his_dictionary` VALUES ('11', '药品分类', '1', '0', '0', '1508983676', '0', '', '1');
INSERT INTO `dzm_his_dictionary` VALUES ('12', '药品单位', '1', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('13', '药品剂型', '1', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('14', '诊断信息', '2', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('15', '医嘱信息', '2', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('16', '项目类型', '3', '0', '0', '1508983676', '0', '', '1');
INSERT INTO `dzm_his_dictionary` VALUES ('17', '项目单位', '3', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('18', '人员分类', '4', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('19', '中草药', '11', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('20', '中成药', '11', '0', '0', '1508983676', '0', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('21', '化验类', '16', '1', '1', '1511948889', '1511948927', '', '0');
INSERT INTO `dzm_his_dictionary` VALUES ('22', '拍片类', '16', '1', '1', '1511948943', '0', '', '0');

-- ----------------------------
-- Table structure for dzm_his_doctor
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_doctor`;
CREATE TABLE `dzm_his_doctor` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `true_name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户个人资料真实姓名',
  `age` int(3) DEFAULT '0' COMMENT '年龄',
  `picture` varchar(255) DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0,空1:男  2:女',
  `background` tinyint(1) NOT NULL DEFAULT '0' COMMENT '学历 1：专科  2：本科  3：研究生  4：博士  5：博士后',
  `phone` varchar(11) NOT NULL DEFAULT '0' COMMENT '手机号',
  `mailbox` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `strong` varchar(255) NOT NULL DEFAULT '' COMMENT '擅长',
  `honor` varchar(255) NOT NULL DEFAULT '' COMMENT '荣誉',
  `introduction` text NOT NULL COMMENT '简介',
  `create_time` int(10) NOT NULL COMMENT '注册时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `uid` int(11) NOT NULL COMMENT '用户表userid',
  `ask_price` decimal(10,2) DEFAULT '0.00' COMMENT '咨询价格',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='医生基本信息表';

-- ----------------------------
-- Records of dzm_his_doctor
-- ----------------------------
INSERT INTO `dzm_his_doctor` VALUES ('1', '张三', '25', '', '0', '0', '0', '', '', '', '', '1511949108', '0', '2', '0.00');

-- ----------------------------
-- Table structure for dzm_his_hospital
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_hospital`;
CREATE TABLE `dzm_his_hospital` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `hospital_name` varchar(20) NOT NULL DEFAULT '' COMMENT '医院名称',
  `picture` varchar(255) DEFAULT '' COMMENT '头像',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `hospital_number` varchar(50) NOT NULL DEFAULT '' COMMENT '医院编号',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `hid` int(11) NOT NULL COMMENT '用户表userid',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '医院地址',
  `owner_name` varchar(20) NOT NULL DEFAULT '' COMMENT '所有者姓名',
  `owner_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '所有者手机号',
  `owner_post` varchar(50) NOT NULL DEFAULT '' COMMENT '所属者职务',
  `major_field` varchar(255) NOT NULL DEFAULT '' COMMENT '专业方向',
  `introduction` varchar(255) NOT NULL DEFAULT '' COMMENT '诊所简介',
  PRIMARY KEY (`id`),
  KEY `uid` (`hid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='HIS医院基本信息表';

-- ----------------------------
-- Records of dzm_his_hospital
-- ----------------------------
INSERT INTO `dzm_his_hospital` VALUES ('1', 'web01', '', '1511947869', '', '0', '1', '', 'web01', '', '', '', '');

-- ----------------------------
-- Table structure for dzm_his_hospital_doctor_relation
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_hospital_doctor_relation`;
CREATE TABLE `dzm_his_hospital_doctor_relation` (
  `rid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `hospital_id` int(10) NOT NULL COMMENT '医院id',
  `department_id` int(10) NOT NULL COMMENT '科室id',
  `physicianid` int(10) NOT NULL COMMENT '医生id',
  `right_list` text NOT NULL COMMENT '权限',
  `title_level` int(10) NOT NULL COMMENT '职称',
  PRIMARY KEY (`rid`),
  KEY `editdate` (`update_time`) USING BTREE,
  KEY `dzm_his_info_23_physicianid_index` (`physicianid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='医生与医院关联表';

-- ----------------------------
-- Records of dzm_his_hospital_doctor_relation
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_hospital_medicines_relation
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_hospital_medicines_relation`;
CREATE TABLE `dzm_his_hospital_medicines_relation` (
  `hmr_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `medicines_id` int(10) NOT NULL COMMENT '药品id',
  `hospital_id` int(10) NOT NULL COMMENT '医院id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`hmr_id`),
  KEY `editdate` (`update_time`) USING BTREE,
  KEY `dzm_his_info_23_physicianid_index` (`hospital_id`) USING BTREE,
  KEY `medicines_id` (`medicines_id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COMMENT='医院药品关联表';

-- ----------------------------
-- Records of dzm_his_hospital_medicines_relation
-- ----------------------------
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('1', '1', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('2', '2', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('3', '3', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('4', '4', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('5', '5', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('6', '6', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('7', '7', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('8', '8', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('9', '9', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('10', '10', '1', '1511949167', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('11', '21', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('12', '22', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('13', '23', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('14', '24', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('15', '25', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('16', '26', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('17', '27', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('18', '28', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('19', '29', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('20', '30', '1', '1511949174', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('21', '41', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('22', '42', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('23', '43', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('24', '44', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('25', '45', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('26', '46', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('27', '47', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('28', '48', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('29', '49', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('30', '50', '1', '1511949179', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('31', '61', '1', '1511949189', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('32', '62', '1', '1511949189', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('33', '63', '1', '1511949189', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('34', '64', '1', '1511949189', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('35', '65', '1', '1511949189', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('36', '66', '1', '1511949189', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('37', '67', '1', '1511949189', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('38', '68', '1', '1511949189', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('39', '69', '1', '1511949189', '0');
INSERT INTO `dzm_his_hospital_medicines_relation` VALUES ('40', '70', '1', '1511949189', '0');

-- ----------------------------
-- Table structure for dzm_his_inspectionfee
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_inspectionfee`;
CREATE TABLE `dzm_his_inspectionfee` (
  `ins_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(10) NOT NULL DEFAULT '0' COMMENT '添加用户id',
  `hid` int(10) NOT NULL DEFAULT '0' COMMENT '医院id',
  `inspection_name` varchar(50) NOT NULL DEFAULT '' COMMENT '项目名称',
  `class` varchar(50) NOT NULL DEFAULT '' COMMENT '项目类别',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '项目单价',
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '项目成本',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `unit` varchar(50) NOT NULL DEFAULT '' COMMENT '单位',
  `class_id` int(10) NOT NULL DEFAULT '0' COMMENT '类别id',
  UNIQUE KEY `pre_id` (`ins_id`) USING BTREE,
  KEY `hid` (`hid`) USING BTREE,
  KEY `inspection_name` (`inspection_name`) USING BTREE,
  KEY `class` (`class`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='检查项目费用表';

-- ----------------------------
-- Records of dzm_his_inspectionfee
-- ----------------------------
INSERT INTO `dzm_his_inspectionfee` VALUES ('1', '1', '1', 'X光', '拍片类', '20.00', '5.00', '1511948965', '0', '次', '22');
INSERT INTO `dzm_his_inspectionfee` VALUES ('2', '1', '1', 'CT', '拍片类', '50.00', '10.00', '1511948984', '0', '次', '22');
INSERT INTO `dzm_his_inspectionfee` VALUES ('3', '1', '1', '血常规', '化验类', '5.00', '1.00', '1511949016', '0', '次', '21');
INSERT INTO `dzm_his_inspectionfee` VALUES ('4', '1', '1', '乙肝五项', '化验类', '30.00', '10.00', '1511949064', '0', '次', '21');

-- ----------------------------
-- Table structure for dzm_his_inventory
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_inventory`;
CREATE TABLE `dzm_his_inventory` (
  `inventory_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '库存ID',
  `hmr_id` int(10) NOT NULL COMMENT '药品ID',
  `company_id` int(10) NOT NULL COMMENT '诊所ID',
  `inventory_num` bigint(20) NOT NULL COMMENT '库存数量',
  `inventory_unit` varchar(50) NOT NULL COMMENT '单位',
  `inventory_trade_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '批发价',
  `inventory_prescription_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '处方价',
  `inventory_trade_total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '批发额',
  `inventory_prescription_total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '处方额',
  `early_warning` varchar(50) NOT NULL DEFAULT '0' COMMENT '库存预警',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`inventory_id`),
  KEY `hmr_id` (`hmr_id`) USING BTREE,
  KEY `company_id` (`company_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='库存表';

-- ----------------------------
-- Records of dzm_his_inventory
-- ----------------------------
INSERT INTO `dzm_his_inventory` VALUES ('1', '40', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('2', '39', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('3', '38', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('4', '37', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('5', '36', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('6', '35', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('7', '34', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('8', '33', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('9', '32', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('10', '31', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('11', '30', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('12', '29', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('13', '28', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('14', '27', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('15', '26', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('16', '25', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('17', '24', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('18', '23', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('19', '22', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('20', '21', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('21', '20', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('22', '19', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('23', '18', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('24', '17', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('25', '16', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('26', '15', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('27', '14', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('28', '13', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('29', '12', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');
INSERT INTO `dzm_his_inventory` VALUES ('30', '11', '1', '19998', '', '15.00', '47.00', '299970.00', '939906.00', '0', '1511949357');

-- ----------------------------
-- Table structure for dzm_his_mchpay
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_mchpay`;
CREATE TABLE `dzm_his_mchpay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hospital_id` int(10) unsigned DEFAULT '0' COMMENT '所属医院id',
  `cash_out_id` int(10) unsigned DEFAULT '0' COMMENT '提现申请id',
  `ip` varchar(32) DEFAULT NULL COMMENT '操作ip',
  `partner_trade_no` varchar(32) DEFAULT NULL COMMENT '商户订单号',
  `payment_no` varchar(32) DEFAULT '' COMMENT '支付平台交易订单',
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态：0未处理，1成功，2失败，3异常',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='提现付款记录，微信企业付款记录';

-- ----------------------------
-- Records of dzm_his_mchpay
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_medicines
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_medicines`;
CREATE TABLE `dzm_his_medicines` (
  `medicines_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `medicines_number` varchar(60) NOT NULL DEFAULT '' COMMENT '药品编号',
  `medicines_name` varchar(120) NOT NULL DEFAULT '' COMMENT '药品名称',
  `medicines_class` varchar(50) NOT NULL DEFAULT '' COMMENT '药品分类 ',
  `prescription_type` varchar(50) NOT NULL DEFAULT '' COMMENT '处方类型',
  `unit` varchar(50) NOT NULL DEFAULT '' COMMENT '单位（g/条）',
  `conversion` int(10) NOT NULL DEFAULT '1' COMMENT '换算量',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `producter` varchar(50) DEFAULT '' COMMENT '生产厂家',
  PRIMARY KEY (`medicines_id`),
  KEY `goods_sn` (`medicines_number`) USING BTREE,
  KEY `last_update` (`update_time`) USING BTREE,
  KEY `medicines_name` (`medicines_name`)
) ENGINE=MyISAM AUTO_INCREMENT=530 DEFAULT CHARSET=utf8 COMMENT='药品信息表';

-- ----------------------------
-- Records of dzm_his_medicines
-- ----------------------------
INSERT INTO `dzm_his_medicines` VALUES ('1', 'dzm0001', '阿胶珠', '中草药', '中药处方', 'g', '1', 'ajz', '1500979674', '1501037957', '');
INSERT INTO `dzm_his_medicines` VALUES ('2', 'dzm0002', '醋艾炭', '中草药', '中药处方', 'g', '1', 'cat', '1500979674', '1501037959', '');
INSERT INTO `dzm_his_medicines` VALUES ('3', 'dzm0003', '制巴戟天', '中草药', '中药处方', 'g', '1', 'zbjt', '1500979674', '1501037960', '');
INSERT INTO `dzm_his_medicines` VALUES ('4', 'dzm0004', '白蔹', '中草药', '中药处方', 'g', '1', 'bl', '1500979674', '1501037961', '');
INSERT INTO `dzm_his_medicines` VALUES ('5', 'dzm0005', '白果', '中草药', '中药处方', 'g', '1', 'bg', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('6', 'dzm0006', '白芨', '中草药', '中药处方', 'g', '1', 'bj', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('7', 'dzm0007', '白芍', '中草药', '中药处方', 'g', '1', 'bs', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('8', 'dzm0008', '白英', '中草药', '中药处方', 'g', '1', 'by', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('9', 'dzm0009', '白芷', '中草药', '中药处方', 'g', '1', 'bz', '1500979674', '1503541979', '');
INSERT INTO `dzm_his_medicines` VALUES ('10', 'dzm0010', '炒牵牛子', '中草药', '中药处方', 'g', '1', 'cqnz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('11', 'dzm0011', '白豆蔻', '中草药', '中药处方', 'g', '1', 'bdk', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('12', 'dzm0012', '制白附子', '中草药', '中药处方', 'g', '1', 'zbfz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('13', 'dzm0013', '金钱白花蛇', '中草药', '中药处方', '条', '1', 'jqbhs', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('14', 'dzm0014', '白花蛇舌草', '中草药', '中药处方', 'g', '1', 'bhssc', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('15', 'dzm0015', '半枝莲', '中草药', '中药处方', 'g', '1', 'bzl', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('16', 'dzm0016', '盐蒺藜', '中草药', '中药处方', 'g', '1', 'yjl', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('17', 'dzm0017', '白梅花', '中草药', '中药处方', 'g', '1', 'bmh', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('18', 'dzm0018', '白前', '中草药', '中药处方', 'g', '1', 'bq', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('19', 'dzm0019', '白头翁', '中草药', '中药处方', 'g', '1', 'btw', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('20', 'dzm0020', '白薇', '中草药', '中药处方', 'g', '1', 'bw', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('21', 'dzm0021', '白鲜皮', '中草药', '中药处方', 'g', '1', 'bxp', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('22', 'dzm0022', '百合', '中草药', '中药处方', 'g', '1', 'bh', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('23', 'dzm0023', '柏子仁', '中草药', '中药处方', 'g', '1', 'bzr', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('24', 'dzm0024', '北败酱草', '中草药', '中药处方', 'g', '1', 'bbjc', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('25', 'dzm0025', '板蓝根', '中草药', '中药处方', 'g', '1', 'blg', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('26', 'dzm0026', '北沙参', '中草药', '中药处方', 'g', '1', 'bss', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('27', 'dzm0027', '荜茇', '中草药', '中药处方', 'g', '1', 'bb', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('28', 'dzm0028', '绵萆薢', '中草药', '中药处方', 'g', '1', 'mbx', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('29', 'dzm0029', '萹蓄', '中草药', '中药处方', 'g', '1', 'bx', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('30', 'dzm0030', '醋鳖甲', '中草药', '中药处方', 'g', '1', 'cbj', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('31', 'dzm0031', '薄荷', '中草药', '中药处方', 'g', '1', 'bh', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('32', 'dzm0032', '伏龙肝', '中草药', '中药处方', 'g', '1', 'flg', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('33', 'dzm0033', '茯苓', '中草药', '中药处方', 'g', '1', 'fl', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('34', 'dzm0034', '茯苓皮', '中草药', '中药处方', 'g', '1', 'flp', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('35', 'dzm0035', '茯神', '中草药', '中药处方', 'g', '1', 'fs', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('36', 'dzm0036', '浮萍', '中草药', '中药处方', 'g', '1', 'fp', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('37', 'dzm0037', '浮小麦', '中草药', '中药处方', 'g', '1', 'fxm', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('38', 'dzm0038', '覆盆子', '中草药', '中药处方', 'g', '1', 'fpz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('39', 'dzm0039', '木通', '中草药', '中药处方', 'g', '1', 'mt', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('40', 'dzm0040', '山慈菇', '中草药', '中药处方', 'g', '1', 'scg', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('41', 'dzm0041', '广藿香', '中草药', '中药处方', 'g', '1', 'ghx', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('42', 'dzm0042', '干姜', '中草药', '中药处方', 'g', '1', 'gj', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('43', 'dzm0043', '甘草', '中草药', '中药处方', 'g', '1', 'gc', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('44', 'dzm0044', '甘松', '中草药', '中药处方', 'g', '1', 'gs', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('45', 'dzm0045', '高良姜', '中草药', '中药处方', 'g', '1', 'glj', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('46', 'dzm0046', '藁本', '中草药', '中药处方', 'g', '1', 'gb', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('47', 'dzm0047', '葛根', '中草药', '中药处方', 'g', '1', 'gg', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('48', 'dzm0048', '钩藤', '中草药', '中药处方', 'g', '1', 'gt', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('49', 'dzm0049', '烫狗脊', '中草药', '中药处方', 'g', '1', 'tgj', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('50', 'dzm0050', '枸杞子', '中草药', '中药处方', 'g', '1', 'gqz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('51', 'dzm0051', '烫骨碎补', '中草药', '中药处方', 'g', '1', 'tgsb', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('52', 'dzm0052', '瓜蒌皮', '中草药', '中药处方', 'g', '1', 'gjp', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('53', 'dzm0053', '蜜瓜蒌子', '中草药', '中药处方', 'g', '1', 'mgjz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('54', 'dzm0054', '海风藤', '中草药', '中药处方', 'g', '1', 'hft', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('55', 'dzm0055', '海金沙', '中草药', '中药处方', 'g', '1', 'hjs', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('56', 'dzm0056', '海螵蛸', '中草药', '中药处方', 'g', '1', 'hps', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('57', 'dzm0057', '海藻', '中草药', '中药处方', 'g', '1', 'hz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('58', 'dzm0058', '柯子肉', '中草药', '中药处方', 'g', '1', 'kzr', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('59', 'dzm0059', '合欢花', '中草药', '中药处方', 'g', '1', 'hhh', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('60', 'dzm0060', '南沙参', '中草药', '中药处方', 'g', '1', 'nss', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('61', 'dzm0061', '酒女贞子', '中草药', '中药处方', 'g', '1', 'jnzz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('62', 'dzm0062', '藕节', '中草药', '中药处方', 'g', '1', 'oj', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('63', 'dzm0063', '藕节炭', '中草药', '中药处方', 'g', '1', 'ojt', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('64', 'dzm0064', '胖大海', '中草药', '中药处方', 'g', '1', 'pdh', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('65', 'dzm0065', '炮姜', '中草药', '中药处方', 'g', '1', 'pj', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('66', 'dzm0066', '佩兰', '中草药', '中药处方', 'g', '1', 'pl', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('67', 'dzm0067', '炙枇杷叶', '中草药', '中药处方', 'g', '1', 'zbby', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('68', 'dzm0068', '蒲公英', '中草药', '中药处方', 'g', '1', 'pgy', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('69', 'dzm0069', '盐补骨脂', '中草药', '中药处方', 'g', '1', 'ybgz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('70', 'dzm0070', '白矾', '中草药', '中药处方', 'g', '1', 'bf', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('71', 'dzm0071', '炒半夏曲', '中草药', '中药处方', 'g', '1', 'cbxq', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('72', 'dzm0072', '炒芡实', '中草药', '中药处方', 'g', '1', 'cqs', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('73', 'dzm0073', '麸炒山药', '中草药', '中药处方', 'g', '1', 'fcsy', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('74', 'dzm0074', '炒山楂', '中草药', '中药处方', 'g', '1', 'csc', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('75', 'dzm0075', '生神曲', '中草药', '中药处方', 'g', '1', 'ssq', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('76', 'dzm0076', '炒紫苏子', '中草药', '中药处方', 'g', '1', 'czsz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('77', 'dzm0077', '麸炒薏苡仁', '中草药', '中药处方', 'g', '1', 'fcyyr', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('78', 'dzm0078', '炒栀子', '中草药', '中药处方', 'g', '1', 'czz', '1500979674', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('79', 'dzm0079', '麸炒枳壳', '中草药', '中药处方', 'g', '1', 'fczk', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('80', 'dzm0080', '车前草', '中草药', '中药处方', 'g', '1', 'cqc', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('81', 'dzm0081', '车前子', '中草药', '中药处方', 'g', '1', 'cqz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('82', 'dzm0082', '陈皮', '中草药', '中药处方', 'g', '1', 'cp', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('83', 'dzm0083', '陈皮炭', '中草药', '中药处方', 'g', '1', 'cpt', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('84', 'dzm0084', '赤芍', '中草药', '中药处方', 'g', '1', 'cs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('85', 'dzm0085', '赤小豆', '中草药', '中药处方', 'g', '1', 'cxd', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('86', 'dzm0086', '炒稻芽', '中草药', '中药处方', 'g', '1', 'cdy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('87', 'dzm0087', '炒谷芽', '中草药', '中药处方', 'g', '1', 'cgy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('88', 'dzm0088', '炒槐花', '中草药', '中药处方', 'g', '1', 'chh', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('89', 'dzm0089', '炒芥子', '中草药', '中药处方', 'g', '1', 'cjz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('90', 'dzm0090', '炒苦杏仁', '中草药', '中药处方', 'g', '1', 'ckxr', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('91', 'dzm0091', '炒麦芽', '中草药', '中药处方', 'g', '1', 'cmy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('92', 'dzm0092', '蚕砂', '中草药', '中药处方', 'g', '1', 'cs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('93', 'dzm0093', '炒苍耳子', '中草药', '中药处方', 'g', '1', 'ccez', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('94', 'dzm0094', '草豆蔻', '中草药', '中药处方', 'g', '1', 'cdk', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('95', 'dzm0095', '炒草果仁', '中草药', '中药处方', 'g', '1', 'ccgr', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('96', 'dzm0096', '侧柏炭', '中草药', '中药处方', 'g', '1', 'cbt', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('97', 'dzm0097', '柴胡', '中草药', '中药处方', 'g', '1', 'ch', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('98', 'dzm0098', '蝉蜕', '中草药', '中药处方', 'g', '1', 'ct', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('99', 'dzm0099', '川贝母', '中草药', '中药处方', 'g', '1', 'cbm', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('100', 'dzm0100', '合欢皮', '中草药', '中药处方', 'g', '1', 'hhp', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('101', 'dzm0101', '制何首乌', '中草药', '中药处方', 'g', '1', 'zhsw', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('102', 'dzm0102', '荷梗', '中草药', '中药处方', 'g', '1', 'hg', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('103', 'dzm0103', '荷叶', '中草药', '中药处方', 'g', '1', 'hy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('104', 'dzm0104', '炒牵牛子', '中草药', '中药处方', 'g', '1', 'cqnz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('105', 'dzm0105', '黑附片', '中草药', '中药处方', 'g', '1', 'hfp', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('106', 'dzm0106', '黑芝麻', '中草药', '中药处方', 'g', '1', 'hzm', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('107', 'dzm0107', '红参', '中草药', '中药处方', 'g', '1', 'hs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('108', 'dzm0108', '红花', '中草药', '中药处方', 'g', '1', 'hh', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('109', 'dzm0109', '鸡冠花', '中草药', '中药处方', 'g', '1', 'jgh', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('110', 'dzm0110', '红藤', '中草药', '中药处方', 'g', '1', 'ht', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('111', 'dzm0111', '厚朴', '中草药', '中药处方', 'g', '1', 'hp', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('112', 'dzm0112', '厚朴花', '中草药', '中药处方', 'g', '1', 'hph', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('113', 'dzm0113', '胡黄连', '中草药', '中药处方', 'g', '1', 'hhl', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('114', 'dzm0114', '虎杖', '中草药', '中药处方', 'g', '1', 'hz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('115', 'dzm0115', '滑石块', '中草药', '中药处方', 'g', '1', 'hsk', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('116', 'dzm0116', '化橘红', '中草药', '中药处方', 'g', '1', 'hjh', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('117', 'dzm0117', '黄柏', '中草药', '中药处方', 'g', '1', 'hb', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('118', 'dzm0118', '黄连', '中草药', '中药处方', 'g', '1', 'hl', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('119', 'dzm0119', '黄芩片', '中草药', '中药处方', 'g', '1', 'hqp', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('120', 'dzm0120', '黄药子', '中草药', '中药处方', 'g', '1', 'hyz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('121', 'dzm0121', '火麻仁', '中草药', '中药处方', 'g', '1', 'hmr', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('122', 'dzm0122', '槐花炭', '中草药', '中药处方', 'g', '1', 'hht', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('123', 'dzm0123', '红景天', '中草药', '中药处方', 'g', '1', 'hjt', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('124', 'dzm0124', '菊花', '中草药', '中药处方', 'g', '1', 'jh', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('125', 'dzm0125', '急性子', '中草药', '中药处方', 'g', '1', 'jxz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('126', 'dzm0126', '僵蚕', '中草药', '中药处方', 'g', '1', 'jc', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('127', 'dzm0127', '焦麦芽', '中草药', '中药处方', 'g', '1', 'jmy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('128', 'dzm0128', '山萘', '中草药', '中药处方', 'g', '1', 'sn', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('129', 'dzm0129', '酒山茱萸', '中草药', '中药处方', 'g', '1', 'jszy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('130', 'dzm0130', '蛇莓', '中草药', '中药处方', 'g', '1', 'sm', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('131', 'dzm0131', '蛇床子', '中草药', '中药处方', 'g', '1', 'scz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('132', 'dzm0132', '蛇蜕', '中草药', '中药处方', 'g', '1', 'st', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('133', 'dzm0133', '射干', '中草药', '中药处方', 'g', '1', 'sg', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('134', 'dzm0134', '伸筋草', '中草药', '中药处方', 'g', '1', 'sjc', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('135', 'dzm0135', '升麻', '中草药', '中药处方', 'g', '1', 'sm', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('136', 'dzm0136', '升麻炭', '中草药', '中药处方', 'g', '1', 'smt', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('137', 'dzm0137', '艾叶', '中草药', '中药处方', 'g', '1', 'ay', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('138', 'dzm0138', '生白术', '中草药', '中药处方', 'g', '1', 'sbs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('139', 'dzm0139', '百部', '中草药', '中药处方', 'g', '1', 'bb', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('140', 'dzm0140', '生槟榔', '中草药', '中药处方', 'g', '1', 'sbl', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('141', 'dzm0141', '麸炒白术', '中草药', '中药处方', 'g', '1', 'fcbs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('142', 'dzm0142', '炒白扁豆', '中草药', '中药处方', 'g', '1', 'cbbd', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('143', 'dzm0143', '麸炒苍术', '中草药', '中药处方', 'g', '1', 'fccs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('144', 'dzm0144', '茺蔚子', '中草药', '中药处方', 'g', '1', 'cwz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('145', 'dzm0145', '抽葫芦', '中草药', '中药处方', 'g', '1', 'chl', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('146', 'dzm0146', '楮实子', '中草药', '中药处方', 'g', '1', 'csz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('147', 'dzm0147', '川芎', '中草药', '中药处方', 'g', '1', 'cx', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('148', 'dzm0148', '川牛膝', '中草药', '中药处方', 'g', '1', 'cnx', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('149', 'dzm0149', '穿山龙', '中草药', '中药处方', 'g', '1', 'csl', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('150', 'dzm0150', '垂盆草', '中草药', '中药处方', 'g', '1', 'cpc', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('151', 'dzm0151', '炒椿皮', '中草药', '中药处方', 'g', '1', 'ccp', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('152', 'dzm0152', '醋柴胡', '中草药', '中药处方', 'g', '1', 'cch', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('153', 'dzm0153', '大枫子肉', '中草药', '中药处方', 'g', '1', 'dfzr', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('154', 'dzm0154', '生大黄', '中草药', '中药处方', 'g', '1', 'sdh', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('155', 'dzm0155', '大黄炭', '中草药', '中药处方', 'g', '1', 'dht', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('156', 'dzm0156', '大青叶', '中草药', '中药处方', 'g', '1', 'dqy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('157', 'dzm0157', '大枣', '中草药', '中药处方', 'g', '1', 'dz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('158', 'dzm0158', '代代花', '中草药', '中药处方', 'g', '1', 'ddh', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('159', 'dzm0159', '黛蛤散', '中草药', '中药处方', 'g', '1', 'dhs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('160', 'dzm0160', '丹参', '中草药', '中药处方', 'g', '1', 'ds', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('161', 'dzm0161', '胆南星', '中草药', '中药处方', 'g', '1', 'dnx', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('162', 'dzm0162', '当归', '中草药', '中药处方', 'g', '1', 'dg', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('163', 'dzm0163', '党参', '中草药', '中药处方', 'g', '1', 'ds', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('164', 'dzm0164', '灯心草', '中草药', '中药处方', 'g', '1', 'dxc', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('165', 'dzm0165', '地肤子', '中草药', '中药处方', 'g', '1', 'dfz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('166', 'dzm0166', '地骨皮', '中草药', '中药处方', 'g', '1', 'dgp', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('167', 'dzm0167', '地榆炭', '中草药', '中药处方', 'g', '1', 'dyt', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('168', 'dzm0168', '公丁香', '中草药', '中药处方', 'g', '1', 'gdx', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('169', 'dzm0169', '冬瓜皮', '中草药', '中药处方', 'g', '1', 'dgp', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('170', 'dzm0170', '炒冬瓜子', '中草药', '中药处方', 'g', '1', 'cdgz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('171', 'dzm0171', '冬葵子', '中草药', '中药处方', 'g', '1', 'dkz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('172', 'dzm0172', '独活', '中草药', '中药处方', 'g', '1', 'dh', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('173', 'dzm0173', '杜仲炭', '中草药', '中药处方', 'g', '1', 'dzt', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('174', 'dzm0174', '煅赤石脂', '中草药', '中药处方', 'g', '1', 'dcsz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('175', 'dzm0175', '煅蛤壳', '中草药', '中药处方', 'g', '1', 'dhk', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('176', 'dzm0176', '煅海浮石', '中草药', '中药处方', 'g', '1', 'dhfs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('177', 'dzm0177', '煅龙齿', '中草药', '中药处方', 'g', '1', 'dlc', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('178', 'dzm0178', '煅龙骨', '中草药', '中药处方', 'g', '1', 'dlg', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('179', 'dzm0179', '煅牡蛎', '中草药', '中药处方', 'g', '1', 'dml', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('180', 'dzm0180', '煅瓦楞子', '中草药', '中药处方', 'g', '1', 'dwlz', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('181', 'dzm0181', '煅赭石', '中草药', '中药处方', 'g', '1', 'dzs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('182', 'dzm0182', '煅寒水石', '中草药', '中药处方', 'g', '1', 'dhss', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('183', 'dzm0183', '煅紫石英', '中草药', '中药处方', 'g', '1', 'dzsy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('184', 'dzm0184', '地龙', '中草药', '中药处方', 'g', '1', 'dl', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('185', 'dzm0185', '醋莪术', '中草药', '中药处方', 'g', '1', 'ces', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('186', 'dzm0186', '鹅不食草', '中草药', '中药处方', 'g', '1', 'ebsc', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('187', 'dzm0187', '儿茶', '中草药', '中药处方', 'g', '1', 'ec', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('188', 'dzm0188', '法半夏', '中草药', '中药处方', 'g', '1', 'fbx', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('189', 'dzm0189', '番泻叶', '中草药', '中药处方', 'g', '1', 'fxy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('190', 'dzm0190', '防风', '中草药', '中药处方', 'g', '1', 'ff', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('191', 'dzm0191', '防己', '中草药', '中药处方', 'g', '1', 'fj', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('192', 'dzm0192', '分心木', '中草药', '中药处方', 'g', '1', 'fxm', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('193', 'dzm0193', '蜂房', '中草药', '中药处方', 'g', '1', 'ff', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('194', 'dzm0194', '凤尾草', '中草药', '中药处方', 'g', '1', 'fwc', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('195', 'dzm0195', '佛手', '中草药', '中药处方', 'g', '1', 'fs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('196', 'dzm0196', '薤白', '中草药', '中药处方', 'g', '1', 'xb', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('197', 'dzm0197', '辛夷', '中草药', '中药处方', 'g', '1', 'xy', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('198', 'dzm0198', '雄黑豆', '中草药', '中药处方', 'g', '1', 'xhd', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('199', 'dzm0199', '徐长卿', '中草药', '中药处方', 'g', '1', 'xcq', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('200', 'dzm0200', '续断', '中草药', '中药处方', 'g', '1', 'xd', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('201', 'dzm0201', '玄参', '中草药', '中药处方', 'g', '1', 'xs', '1500979675', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('202', 'dzm0202', '旋复花', '中草药', '中药处方', 'g', '1', 'xfh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('203', 'dzm0203', '血余炭', '中草药', '中药处方', 'g', '1', 'xyt', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('204', 'dzm0204', '醋延胡索', '中草药', '中药处方', 'g', '1', 'cyhs', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('205', 'dzm0205', '盐知母', '中草药', '中药处方', 'g', '1', 'yzm', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('206', 'dzm0206', '野菊花', '中草药', '中药处方', 'g', '1', 'yjh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('207', 'dzm0207', '盐益智仁', '中草药', '中药处方', 'g', '1', 'yyzr', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('208', 'dzm0208', '绵茵陈', '中草药', '中药处方', 'g', '1', 'myc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('209', 'dzm0209', '炙淫羊藿', '中草药', '中药处方', 'g', '1', 'zyyh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('210', 'dzm0210', '鱼腥草', '中草药', '中药处方', 'g', '1', 'yxc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('211', 'dzm0211', '玉竹', '中草药', '中药处方', 'g', '1', 'yz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('212', 'dzm0212', '郁金', '中草药', '中药处方', 'g', '1', 'yj', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('213', 'dzm0213', '预知子', '中草药', '中药处方', 'g', '1', 'yzz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('214', 'dzm0214', '制远志', '中草药', '中药处方', 'g', '1', 'zyz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('215', 'dzm0215', '月季花', '中草药', '中药处方', 'g', '1', 'yjh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('216', 'dzm0216', '银杏叶', '中草药', '中药处方', 'g', '1', 'yxy', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('217', 'dzm0217', '泽兰', '中草药', '中药处方', 'g', '1', 'zl', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('218', 'dzm0218', '泽泻', '中草药', '中药处方', 'g', '1', 'zx', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('219', 'dzm0219', '浙贝母', '中草药', '中药处方', 'g', '1', 'zbm', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('220', 'dzm0220', '烫枳实', '中草药', '中药处方', 'g', '1', 'tzs', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('221', 'dzm0221', '蜜百部', '中草药', '中药处方', 'g', '1', 'mbb', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('222', 'dzm0222', '酒黄精', '中草药', '中药处方', 'g', '1', 'jhj', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('223', 'dzm0223', '炙黄芪', '中草药', '中药处方', 'g', '1', 'zhc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('224', 'dzm0224', '猪苓', '中草药', '中药处方', 'g', '1', 'zl', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('225', 'dzm0225', '淡竹叶', '中草药', '中药处方', 'g', '1', 'dzy', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('226', 'dzm0226', '紫草', '中草药', '中药处方', 'g', '1', 'zc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('227', 'dzm0227', '紫苏梗', '中草药', '中药处方', 'g', '1', 'zsg', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('228', 'dzm0228', '炙前胡', '中草药', '中药处方', 'g', '1', 'zqh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('229', 'dzm0229', '金莲花', '中草药', '中药处方', 'g', '1', 'jlh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('230', 'dzm0230', '金钱草', '中草药', '中药处方', 'g', '1', 'jqc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('231', 'dzm0231', '酒大黄', '中草药', '中药处方', 'g', '1', 'jdh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('232', 'dzm0232', '酒当归', '中草药', '中药处方', 'g', '1', 'jdg', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('233', 'dzm0233', '炒决明子', '中草药', '中药处方', 'g', '1', 'cjmz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('234', 'dzm0234', '鸡血藤', '中草药', '中药处方', 'g', '1', 'jxt', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('235', 'dzm0235', '苦参', '中草药', '中药处方', 'g', '1', 'ks', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('236', 'dzm0236', '款冬花', '中草药', '中药处方', 'g', '1', 'kdh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('237', 'dzm0237', '炒莱菔子', '中草药', '中药处方', 'g', '1', 'clfz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('238', 'dzm0238', '桂枝', '中草药', '中药处方', 'g', '1', 'gz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('239', 'dzm0239', '连翘', '中草药', '中药处方', 'g', '1', 'lq', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('240', 'dzm0240', '凌霄花', '中草药', '中药处方', 'g', '1', 'lxh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('241', 'dzm0241', '芦根', '中草药', '中药处方', 'g', '1', 'lg', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('242', 'dzm0242', '鹿角镑', '中草药', '中药处方', 'g', '1', 'ljb', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('243', 'dzm0243', '路路通', '中草药', '中药处方', 'g', '1', 'llt', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('244', 'dzm0244', '络石藤', '中草药', '中药处方', 'g', '1', 'lst', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('245', 'dzm0245', '麻黄根', '中草药', '中药处方', 'g', '1', 'mhg', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('246', 'dzm0246', '生蔓荆子', '中草药', '中药处方', 'g', '1', 'smjz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('247', 'dzm0247', '木贼草', '中草药', '中药处方', 'g', '1', 'mzc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('248', 'dzm0248', '石菖蒲', '中草药', '中药处方', 'g', '1', 'scp', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('249', 'dzm0249', '首乌藤', '中草药', '中药处方', 'g', '1', 'swt', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('250', 'dzm0250', '熟大黄', '中草药', '中药处方', 'g', '1', 'sdh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('251', 'dzm0251', '丝瓜络', '中草药', '中药处方', 'g', '1', 'sgl', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('252', 'dzm0252', '炒酸枣仁', '中草药', '中药处方', 'g', '1', 'cszr', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('253', 'dzm0253', '太子参', '中草药', '中药处方', 'g', '1', 'tzs', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('254', 'dzm0254', '炒桃仁', '中草药', '中药处方', 'g', '1', 'ctr', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('255', 'dzm0255', '千年健', '中草药', '中药处方', 'g', '1', 'qnj', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('256', 'dzm0256', '羌活', '中草药', '中药处方', 'g', '1', 'qh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('257', 'dzm0257', '醋青皮', '中草药', '中药处方', 'g', '1', 'cqp', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('258', 'dzm0258', '醋五灵脂', '中草药', '中药处方', 'g', '1', 'cwlz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('259', 'dzm0259', '醋五味子', '中草药', '中药处方', 'g', '1', 'cwwz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('260', 'dzm0260', '威灵仙', '中草药', '中药处方', 'g', '1', 'wlx', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('261', 'dzm0261', '豨签草', '中草药', '中药处方', 'g', '1', 'xqc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('262', 'dzm0262', '夏枯草', '中草药', '中药处方', 'g', '1', 'xkc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('263', 'dzm0263', '仙鹤草', '中草药', '中药处方', 'g', '1', 'xhc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('264', 'dzm0264', '醋香附', '中草药', '中药处方', 'g', '1', 'cxf', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('265', 'dzm0265', '盐小茴香', '中草药', '中药处方', 'g', '1', 'yxhx', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('266', 'dzm0266', '生地榆', '中草药', '中药处方', 'g', '1', 'sdy', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('267', 'dzm0267', '绵马贯众', '中草药', '中药处方', 'g', '1', 'mmgz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('268', 'dzm0268', '生黄芪', '中草药', '中药处方', 'g', '1', 'shc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('269', 'dzm0269', '生薏苡仁', '中草药', '中药处方', 'g', '1', 'syyr', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('270', 'dzm0270', '醋三棱', '中草药', '中药处方', 'g', '1', 'csl', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('271', 'dzm0271', '沙苑子', '中草药', '中药处方', 'g', '1', 'syz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('272', 'dzm0272', '砂仁', '中草药', '中药处方', 'g', '1', 'sr', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('273', 'dzm0273', '片姜黄', '中草药', '中药处方', 'g', '1', 'pjh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('274', 'dzm0274', '盐杜仲', '中草药', '中药处方', 'g', '1', 'ydz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('275', 'dzm0275', '土茯苓', '中草药', '中药处方', 'g', '1', 'tfl', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('276', 'dzm0276', '炙桑白皮', '中草药', '中药处方', 'g', '1', 'zsbp', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('277', 'dzm0277', '生芡实', '中草药', '中药处方', 'g', '1', 'sqs', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('278', 'dzm0278', '菟丝子', '中草药', '中药处方', 'g', '1', 'tsz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('279', 'dzm0279', '牛膝', '中草药', '中药处方', 'g', '1', 'nx', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('280', 'dzm0280', '姜黄', '中草药', '中药处方', 'g', '1', 'jh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('281', 'dzm0281', '姜半夏', '中草药', '中药处方', 'g', '1', 'jbx', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('282', 'dzm0282', '降香', '中草药', '中药处方', 'g', '1', 'jx', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('283', 'dzm0283', '川楝子', '中草药', '中药处方', 'g', '1', 'clz', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('284', 'dzm0284', '穿心莲', '中草药', '中药处方', 'g', '1', 'cxl', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('285', 'dzm0285', '翻白草', '中草药', '中药处方', 'g', '1', 'fbc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('286', 'dzm0286', '盐黄柏', '中草药', '中药处方', 'g', '1', 'yhb', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('287', 'dzm0287', '阳起石', '中草药', '中药处方', 'g', '1', 'yqs', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('288', 'dzm0288', '夜明砂', '中草药', '中药处方', 'g', '1', 'yms', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('289', 'dzm0289', '银柴胡', '中草药', '中药处方', 'g', '1', 'ych', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('290', 'dzm0290', '松节', '中草药', '中药处方', 'g', '1', 'sj', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('291', 'dzm0291', '玉米须', '中草药', '中药处方', 'g', '1', 'ymx', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('292', 'dzm0292', '郁李仁', '中草药', '中药处方', 'g', '1', 'ylr', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('293', 'dzm0293', '金银花炭', '中草药', '中药处方', 'g', '1', 'jyht', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('294', 'dzm0294', '桑螵蛸', '中草药', '中药处方', 'g', '1', 'sps', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('295', 'dzm0295', '石斛', '中草药', '中药处方', 'g', '1', 'sh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('296', 'dzm0296', '皂角刺', '中草药', '中药处方', 'g', '1', 'zjc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('297', 'dzm0297', '珍珠母', '中草药', '中药处方', 'g', '1', 'zzm', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('298', 'dzm0298', '知母', '中草药', '中药处方', 'g', '1', 'zm', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('299', 'dzm0299', '制草乌', '中草药', '中药处方', 'g', '1', 'zcw', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('300', 'dzm0300', '制川乌', '中草药', '中药处方', 'g', '1', 'zcw', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('301', 'dzm0301', '醋没药', '中草药', '中药处方', 'g', '1', 'cmy', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('302', 'dzm0302', '醋乳香', '中草药', '中药处方', 'g', '1', 'crx', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('303', 'dzm0303', '制天南星', '中草药', '中药处方', 'g', '1', 'ztnx', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('304', 'dzm0304', '炙甘草', '中草药', '中药处方', 'g', '1', 'zgc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('305', 'dzm0305', '炙麻黄', '中草药', '中药处方', 'g', '1', 'zmh', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('306', 'dzm0306', '竹茹', '中草药', '中药处方', 'g', '1', 'zr', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('307', 'dzm0307', '苎麻根', '中草药', '中药处方', 'g', '1', 'zmg', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('308', 'dzm0308', '紫河车', '中草药', '中药处方', 'g', '1', 'zhc', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('309', 'dzm0309', '紫苏叶', '中草药', '中药处方', 'g', '1', 'zsy', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('310', 'dzm0310', '棕榈炭', '中草药', '中药处方', 'g', '1', 'zlt', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('311', 'dzm0311', '苦地丁', '中草药', '中药处方', 'g', '1', 'kdd', '1500979676', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('312', 'dzm0312', '蜜紫苑', '中草药', '中药处方', 'g', '1', 'mzy', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('313', 'dzm0313', '炙款冬花', '中草药', '中药处方', 'g', '1', 'zkdh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('314', 'dzm0314', '乌梅', '中草药', '中药处方', 'g', '1', 'wm', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('315', 'dzm0315', '酒乌梢蛇', '中草药', '中药处方', 'g', '1', 'jwss', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('316', 'dzm0316', '焦山楂', '中草药', '中药处方', 'g', '1', 'jsc', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('317', 'dzm0317', '焦栀子', '中草药', '中药处方', 'g', '1', 'jzz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('318', 'dzm0318', '荆芥炭', '中草药', '中药处方', 'g', '1', 'jjt', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('319', 'dzm0319', '焦神曲', '中草药', '中药处方', 'g', '1', 'jsq', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('320', 'dzm0320', '金荞麦', '中草药', '中药处方', 'g', '1', 'jqm', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('321', 'dzm0321', '金银花', '中草药', '中药处方', 'g', '1', 'jyh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('322', 'dzm0322', '金樱子', '中草药', '中药处方', 'g', '1', 'jyz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('323', 'dzm0323', '锦灯笼', '中草药', '中药处方', 'g', '1', 'jdl', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('324', 'dzm0324', '荆芥', '中草药', '中药处方', 'g', '1', 'jj', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('325', 'dzm0325', '荆芥穗', '中草药', '中药处方', 'g', '1', 'jjs', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('326', 'dzm0326', '九香虫', '中草药', '中药处方', 'g', '1', 'jxc', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('327', 'dzm0327', '酒白芍', '中草药', '中药处方', 'g', '1', 'jbs', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('328', 'dzm0328', '酒黄芩', '中草药', '中药处方', 'g', '1', 'jhq', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('329', 'dzm0329', '桔梗', '中草药', '中药处方', 'g', '1', 'jg', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('330', 'dzm0330', '菊花', '中草药', '中药处方', 'g', '1', 'jh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('331', 'dzm0331', '盐橘核', '中草药', '中药处方', 'g', '1', 'yjh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('332', 'dzm0332', '橘络', '中草药', '中药处方', 'g', '1', 'jl', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('333', 'dzm0333', '瞿麦', '中草药', '中药处方', 'g', '1', 'jm', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('334', 'dzm0334', '卷柏', '中草药', '中药处方', 'g', '1', 'jb', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('335', 'dzm0335', '鸡骨草', '中草药', '中药处方', 'g', '1', 'jgc', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('336', 'dzm0336', '醋鸡内金', '中草药', '中药处方', 'g', '1', 'cjnj', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('337', 'dzm0337', '枯矾', '中草药', '中药处方', 'g', '1', 'kf', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('338', 'dzm0338', '昆布', '中草药', '中药处方', 'g', '1', 'kb', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('339', 'dzm0339', '醋龟板', '中草药', '中药处方', 'g', '1', 'cgb', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('340', 'dzm0340', '鬼箭羽', '中草药', '中药处方', 'g', '1', 'gjy', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('341', 'dzm0341', '荔枝核', '中草药', '中药处方', 'g', '1', 'lzh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('342', 'dzm0342', '莲子心', '中草药', '中药处方', 'g', '1', 'lzx', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('343', 'dzm0343', '刘寄奴', '中草药', '中药处方', 'g', '1', 'ljn', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('344', 'dzm0344', '龙葵', '中草药', '中药处方', 'g', '1', 'lk', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('345', 'dzm0345', '龙胆草', '中草药', '中药处方', 'g', '1', 'ldc', '1500979677', '1504923092', '');
INSERT INTO `dzm_his_medicines` VALUES ('346', 'dzm0346', '龙眼肉', '中草药', '中药处方', 'g', '1', 'lyr', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('347', 'dzm0347', '漏芦', '中草药', '中药处方', 'g', '1', 'll', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('348', 'dzm0348', '芦荟', '中草药', '中药处方', 'g', '1', 'lh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('349', 'dzm0349', '鹿角霜', '中草药', '中药处方', 'g', '1', 'ljs', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('350', 'dzm0350', '灵芝', '中草药', '中药处方', 'g', '1', 'lz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('351', 'dzm0351', '马勃', '中草药', '中药处方', 'g', '1', 'mb', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('352', 'dzm0352', '麦冬', '中草药', '中药处方', 'g', '1', 'md', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('353', 'dzm0353', '蔓荆子炭', '中草药', '中药处方', 'g', '1', 'mjzt', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('354', 'dzm0354', '天竺黄', '中草药', '中药处方', 'g', '1', 'tdh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('355', 'dzm0355', '冰片', '中草药', '中药处方', 'g', '1', 'bp', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('356', 'dzm0356', '荜澄茄', '中草药', '中药处方', 'g', '1', 'bcq', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('357', 'dzm0357', '瓜蒌', '中草药', '中药处方', 'g', '1', 'gj', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('358', 'dzm0358', '焦槟榔', '中草药', '中药处方', 'g', '1', 'jbl', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('359', 'dzm0359', '炒槟榔', '中草药', '中药处方', 'g', '1', 'cbl', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('360', 'dzm0360', '大腹皮', '中草药', '中药处方', 'g', '1', 'dfp', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('361', 'dzm0361', '淡豆豉', '中草药', '中药处方', 'g', '1', 'ddc', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('362', 'dzm0362', '益母草', '中草药', '中药处方', 'g', '1', 'ymc', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('363', 'dzm0363', '盐泽泻', '中草药', '中药处方', 'g', '1', 'yzx', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('364', 'dzm0364', '焦酸枣仁', '中草药', '中药处方', 'g', '1', 'jszr', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('365', 'dzm0365', '焦苍术', '中草药', '中药处方', 'g', '1', 'jcs', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('366', 'dzm0366', '橘叶', '中草药', '中药处方', 'g', '1', 'jy', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('367', 'dzm0367', '莲子肉', '中草药', '中药处方', 'g', '1', 'lzr', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('368', 'dzm0368', '熟地黄炭', '中草药', '中药处方', 'g', '1', 'sdht', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('369', 'dzm0369', '谷芽', '中草药', '中药处方', 'g', '1', 'gy', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('370', 'dzm0370', '生鸡内金', '中草药', '中药处方', 'g', '1', 'sjnj', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('371', 'dzm0371', '芒硝', '中草药', '中药处方', 'g', '1', 'mx', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('372', 'dzm0372', '猫爪草', '中草药', '中药处方', 'g', '1', 'mzc', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('373', 'dzm0373', '玉蝴蝶', '中草药', '中药处方', 'g', '1', 'yhd', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('374', 'dzm0374', '石决明', '中草药', '中药处方', 'g', '1', 'sjm', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('375', 'dzm0375', '石榴皮', '中草药', '中药处方', 'g', '1', 'slp', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('376', 'dzm0376', '石伟', '中草药', '中药处方', 'g', '1', 'sw', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('377', 'dzm0377', '使君子', '中草药', '中药处方', 'g', '1', 'sjz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('378', 'dzm0378', '柿蒂', '中草药', '中药处方', 'g', '1', 'sd', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('379', 'dzm0379', '熟地黄', '中草药', '中药处方', 'g', '1', 'sdh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('380', 'dzm0380', '水红花子', '中草药', '中药处方', 'g', '1', 'shhz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('381', 'dzm0381', '水牛角丝', '中草药', '中药处方', 'g', '1', 'snjs', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('382', 'dzm0382', '制水蛭', '中草药', '中药处方', 'g', '1', 'zsz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('383', 'dzm0383', '苏木', '中草药', '中药处方', 'g', '1', 'sm', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('384', 'dzm0384', '锁阳', '中草药', '中药处方', 'g', '1', 'sy', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('385', 'dzm0385', '前胡', '中草药', '中药处方', 'g', '1', 'qh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('386', 'dzm0386', '马齿苋', '中草药', '中药处方', 'g', '1', 'mcw', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('387', 'dzm0387', '茜草', '中草药', '中药处方', 'g', '1', 'qc', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('388', 'dzm0388', '茜草炭', '中草药', '中药处方', 'g', '1', 'qct', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('389', 'dzm0389', '秦艽', '中草药', '中药处方', 'g', '1', 'qj', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('390', 'dzm0390', '秦皮', '中草药', '中药处方', 'g', '1', 'qp', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('391', 'dzm0391', '青蒿', '中草药', '中药处方', 'g', '1', 'qg', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('392', 'dzm0392', '青黛', '中草药', '中药处方', 'g', '1', 'qd', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('393', 'dzm0393', '青风藤', '中草药', '中药处方', 'g', '1', 'qft', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('394', 'dzm0394', '青葙子', '中草药', '中药处方', 'g', '1', 'qxz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('395', 'dzm0395', '清半夏', '中草药', '中药处方', 'g', '1', 'qbx', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('396', 'dzm0396', '蜈蚣', '中草药', '中药处方', '条', '1', 'wg', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('397', 'dzm0397', '制吴茱萸', '中草药', '中药处方', 'g', '1', 'zwzy', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('398', 'dzm0398', '五倍子', '中草药', '中药处方', 'g', '1', 'wbz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('399', 'dzm0399', '炒王不留行', '中草药', '中药处方', 'g', '1', 'cwblx', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('400', 'dzm0400', '西青果', '中草药', '中药处方', 'g', '1', 'xqg', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('401', 'dzm0401', '西洋参', '中草药', '中药处方', 'g', '1', 'xys', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('402', 'dzm0402', '三七块', '中草药', '中药处方', 'g', '1', 'sqk', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('403', 'dzm0403', '三七粉', '中草药', '中药处方', 'g', '1', 'sqf', '1500979677', '1504923478', '');
INSERT INTO `dzm_his_medicines` VALUES ('404', 'dzm0404', '半边莲', '中草药', '中药处方', 'g', '1', 'bbl', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('405', 'dzm0405', '细辛', '中草药', '中药处方', 'g', '1', 'xx', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('406', 'dzm0406', '仙茅', '中草药', '中药处方', 'g', '1', 'xm', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('407', 'dzm0407', '香橼', '中草药', '中药处方', 'g', '1', 'xy', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('408', 'dzm0408', '小蓟', '中草药', '中药处方', 'g', '1', 'xj', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('409', 'dzm0409', '小蓟炭', '中草药', '中药处方', 'g', '1', 'xjt', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('410', 'dzm0410', '藤梨根', '中草药', '中药处方', 'g', '1', 'tlg', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('411', 'dzm0411', '生地黄', '中草药', '中药处方', 'g', '1', 'sdh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('412', 'dzm0412', '生地黄炭', '中草药', '中药处方', 'g', '1', 'sdht', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('413', 'dzm0413', '生杜仲', '中草药', '中药处方', 'g', '1', 'sdz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('414', 'dzm0414', '生蛤壳', '中草药', '中药处方', 'g', '1', 'shk', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('415', 'dzm0415', '生槐花', '中草药', '中药处方', 'g', '1', 'shh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('416', 'dzm0416', '槐米', '中草药', '中药处方', 'g', '1', 'hm', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('417', 'dzm0417', '生龙齿', '中草药', '中药处方', 'g', '1', 'slc', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('418', 'dzm0418', '生龙骨', '中草药', '中药处方', 'g', '1', 'slg', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('419', 'dzm0419', '生麻黄', '中草药', '中药处方', 'g', '1', 'smh', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('420', 'dzm0420', '生何首乌', '中草药', '中药处方', 'g', '1', 'shsw', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('421', 'dzm0421', '生赭石', '中草药', '中药处方', 'g', '1', 'szs', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('422', 'dzm0422', '生栀子', '中草药', '中药处方', 'g', '1', 'szz', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('423', 'dzm0423', '生紫苑', '中草药', '中药处方', 'g', '1', 'szy', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('424', 'dzm0424', '寒水石', '中草药', '中药处方', 'g', '1', 'hss', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('425', 'dzm0425', '松花粉', '中草药', '中药处方', 'g', '1', 'shf', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('426', 'dzm0426', '山药', '中草药', '中药处方', 'g', '1', 'sy', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('427', 'dzm0427', '桑椹', '中草药', '中药处方', 'g', '1', 'ss', '1500979677', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('428', 'dzm0428', '蒲黄炭', '中草药', '中药处方', 'g', '1', 'pht', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('429', 'dzm0429', '天冬', '中草药', '中药处方', 'g', '1', 'td', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('430', 'dzm0430', '天麻', '中草药', '中药处方', 'g', '1', 'tm', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('431', 'dzm0431', '天花粉', '中草药', '中药处方', 'g', '1', 'thf', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('432', 'dzm0432', '天葵子', '中草药', '中药处方', 'g', '1', 'tkz', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('433', 'dzm0433', '葶苈子', '中草药', '中药处方', 'g', '1', 'dlz', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('434', 'dzm0434', '通草', '中草药', '中药处方', 'g', '1', 'tc', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('435', 'dzm0435', '土贝母', '中草药', '中药处方', 'g', '1', 'tbm', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('436', 'dzm0436', '土鳖虫', '中草药', '中药处方', 'g', '1', 'tbc', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('437', 'dzm0437', '土大黄', '中草药', '中药处方', 'g', '1', 'tdh', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('438', 'dzm0438', '侧柏叶', '中草药', '中药处方', 'g', '1', 'cby', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('439', 'dzm0439', '生磁石', '中草药', '中药处方', 'g', '1', 'scs', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('440', 'dzm0440', '全蝎', '中草药', '中药处方', 'g', '1', 'qx', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('441', 'dzm0441', '忍冬藤', '中草药', '中药处方', 'g', '1', 'rdt', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('442', 'dzm0442', '肉桂', '中草药', '中药处方', 'g', '1', 'rg', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('443', 'dzm0443', '酒肉苁蓉', '中草药', '中药处方', 'g', '1', 'jrcr', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('444', 'dzm0444', '肉豆蔻', '中草药', '中药处方', 'g', '1', 'rdk', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('445', 'dzm0445', '桑叶', '中草药', '中药处方', 'g', '1', 'sy', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('446', 'dzm0446', '桑枝', '中草药', '中药处方', 'g', '1', 'sz', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('447', 'dzm0447', '桑寄生', '中草药', '中药处方', 'g', '1', 'sjs', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('448', 'dzm0448', '生麦芽', '中草药', '中药处方', 'g', '1', 'smy', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('449', 'dzm0449', '生牡蛎', '中草药', '中药处方', 'g', '1', 'sml', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('450', 'dzm0450', '生蒲黄', '中草药', '中药处方', 'g', '1', 'sph', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('451', 'dzm0451', '生山楂', '中草药', '中药处方', 'g', '1', 'ssc', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('452', 'dzm0452', '生石膏', '中草药', '中药处方', 'g', '1', 'ssg', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('453', 'dzm0453', '乌药', '中草药', '中药处方', 'g', '1', 'wy', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('454', 'dzm0454', '白茅根', '中草药', '中药处方', 'g', '1', 'bmg', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('455', 'dzm0455', '龟甲胶', '中草药', '中药处方', 'g', '1', 'gjj', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('456', 'dzm0456', '望月砂', '中草药', '中药处方', 'g', '1', 'wys', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('457', 'dzm0457', '玫瑰花', '中草药', '中药处方', 'g', '1', 'mgh', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('458', 'dzm0458', '檀香', '中草药', '中药处方', 'g', '1', 'tx', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('459', 'dzm0459', '拳参', '中草药', '中药处方', 'g', '1', 'qs', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('460', 'dzm0460', '煅金礞石', '中草药', '中药处方', 'g', '1', 'djms', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('461', 'dzm0461', '蜜槐角', '中草药', '中药处方', 'g', '1', 'mhj', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('462', 'dzm0462', '牡丹皮', '中草药', '中药处方', 'g', '1', 'mdp', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('463', 'dzm0463', '罗布麻叶', '中草药', '中药处方', 'g', '1', 'lbmy', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('464', 'dzm0464', '炒牛蒡子', '中草药', '中药处方', 'g', '1', 'cnbz', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('465', 'dzm0465', '烫刺猬皮', '中草药', '中药处方', 'g', '1', 'tcwp', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('466', 'dzm0466', '黄柏炭', '中草药', '中药处方', 'g', '1', 'hbt', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('467', 'dzm0467', '木香', '中草药', '中药处方', 'g', '1', 'mx', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('468', 'dzm0468', '木瓜', '中草药', '中药处方', 'g', '1', 'mg', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('469', 'dzm0469', '土荆皮', '中草药', '中药处方', 'g', '1', 'tjp', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('470', 'dzm0470', '煅磁石', '中草药', '中药处方', 'g', '1', 'dcs', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('471', 'dzm0471', '盐葫芦巴', '中草药', '中药处方', 'g', '1', 'yhlb', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('472', 'dzm0472', '密蒙花', '中草药', '中药处方', 'g', '1', 'mmh', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('473', 'dzm0473', '追地枫', '中草药', '中药处方', 'g', '1', 'zdf', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('474', 'dzm0474', '谷精草', '中草药', '中药处方', 'g', '1', 'gjc', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('475', 'dzm0475', '焦谷芽', '中草药', '中药处方', 'g', '1', 'jgy', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('476', 'dzm0476', '六一散', '中草药', '中药处方', 'g', '1', 'lys', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('477', 'dzm0477', '香加皮', '中草药', '中药处方', 'g', '1', 'xjp', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('478', 'dzm0478', '香薷', '中草药', '中药处方', 'g', '1', 'xr', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('479', 'dzm0479', '北豆根', '中草药', '中药处方', 'g', '1', 'bdg', '1500979678', '1508383709', '');
INSERT INTO `dzm_his_medicines` VALUES ('480', 'dzm0480', '黄芩炭', '中草药', '中药处方', 'g', '1', 'hqt', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('481', 'dzm0481', '茅根炭', '中草药', '中药处方', 'g', '1', 'mgt', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('482', 'dzm0482', '焦白术', '中草药', '中药处方', 'g', '1', 'jbs', '1500979678', '1508383707', '');
INSERT INTO `dzm_his_medicines` VALUES ('483', 'dzm0483', '焦白芍', '中草药', '中药处方', 'g', '1', 'jbs', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('484', 'dzm0484', '绵马贯众炭', '中草药', '中药处方', 'g', '1', 'mmgzt', '1500979678', '1508383706', '');
INSERT INTO `dzm_his_medicines` VALUES ('485', 'dzm0485', '荆芥穗炭', '中草药', '中药处方', 'g', '1', 'jjst', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('486', 'dzm0486', '鹿衔草', '中草药', '中药处方', 'g', '1', 'lxc', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('487', 'dzm0487', '煅自然铜', '中草药', '中药处方', 'g', '1', 'dzrt', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('488', 'dzm0488', '葛花', '中草药', '中药处方', 'g', '1', 'gh', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('489', 'dzm0489', '鹿角胶', '中草药', '中药处方', 'g', '1', 'ljj', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('490', 'dzm0490', '马尾连', '中草药', '中药处方', 'g', '1', 'mwl', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('491', 'dzm0491', '蕲蛇', '中草药', '中药处方', 'g', '1', 'js', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('492', 'dzm0492', '花椒', '中草药', '中药处方', 'g', '1', 'hj', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('493', 'dzm0493', '透骨草', '中草药', '中药处方', 'g', '1', 'tgc', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('494', 'dzm0494', '醋穿山甲', '中草药', '中药处方', 'g', '1', 'ccsj', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('495', 'dzm0495', '阿胶', '中草药', '中药处方', 'g', '1', 'aj', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('496', 'dzm0496', '石见穿', '中草药', '中药处方', 'g', '1', 'sjc', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('497', 'dzm0497', '人参片', '中草药', '中药处方', 'g', '1', 'rsp', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('498', 'dzm0498', '重楼', '中草药', '中药处方', 'g', '1', 'zl', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('499', 'dzm0499', '玄明粉', '中草药', '中药处方', 'g', '1', 'xmf', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('500', 'dzm0500', '墨旱莲', '中草药', '中药处方', 'g', '1', 'mhl', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('501', 'dzm0501', '炒神曲', '中草药', '中药处方', 'g', '1', 'csq', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('502', 'dzm0502', '生桑白皮', '中草药', '中药处方', 'g', '1', 'ssbp', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('503', 'dzm0503', '枸骨叶', '中草药', '中药处方', 'g', '1', 'ggy', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('504', 'dzm0504', '生山茱萸', '中草药', '中药处方', 'g', '1', 'sszy', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('505', 'dzm0505', '生扁豆', '中草药', '中药处方', 'g', '1', 'sbd', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('506', 'dzm0506', '西红花', '中草药', '中药处方', 'g', '1', 'xhh', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('507', 'dzm0507', '焦鸡内金', '中草药', '中药处方', 'g', '1', 'jjnj', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('508', 'dzm0508', '铁树叶', '中草药', '中药处方', 'g', '1', 'tsy', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('509', 'dzm0509', '野生灵芝', '中草药', '中药处方', 'g', '1', 'yslz', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('510', 'dzm0510', '阴起石', '中草药', '中药处方', 'g', '1', 'yqs', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('511', 'dzm0511', '焦稻芽', '中草药', '中药处方', 'g', '1', 'jdy', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('512', 'dzm0512', '煅青礞石', '中草药', '中药处方', 'g', '1', 'dqms', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('513', 'dzm0513', '炙白前', '中草药', '中药处方', 'g', '1', 'zbq', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('514', 'dzm0514', '大黄粉', '中草药', '中药处方', 'g', '1', 'dhf', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('515', 'dzm0515', '泡姜炭', '中草药', '中药处方', 'g', '1', 'pjt', '1500979678', '1508217559', '');
INSERT INTO `dzm_his_medicines` VALUES ('516', 'dzm0516', '韭菜籽', '中草药', '中药处方', 'g', '1', 'jcz', '1500979678', '1508217559', '');
INSERT INTO `dzm_his_medicines` VALUES ('517', 'dzm0517', '炒蒲黄', '中草药', '中药处方', 'g', '1', 'cph', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('518', 'dzm0518', '核桃仁', '中草药', '中药处方', 'g', '1', 'htr', '1500979678', '1501038341', '');
INSERT INTO `dzm_his_medicines` VALUES ('519', 'dzm0519', '建曲', '中草药', '中药处方', 'g', '1', 'jq', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('520', 'dzm0520', '枳椇子', '中草药', '中药处方', 'g', '1', 'zjz', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('521', 'dzm0521', '绞股蓝', '中草药', '中药处方', 'g', '1', 'jgl', '1500979678', '1501038338', '');
INSERT INTO `dzm_his_medicines` VALUES ('522', 'dzm0522', '海马', '中草药', '中药处方', 'g', '1', 'hm', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('523', 'dzm0523', '生姜', '中草药', '中药处方', 'g', '1', 'sj', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('524', 'dzm0524', '鱼脑石', '中草药', '中药处方', 'g', '1', 'yns', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('525', 'dzm0525', '刺五加', '中草药', '中药处方', 'g', '1', 'cwj', '1500979678', '0', '');
INSERT INTO `dzm_his_medicines` VALUES ('526', 'dzm0526', '鸦胆子', '中草药', '中药处方', 'g', '1', 'ydz', '1500979678', '1508217384', '');
INSERT INTO `dzm_his_medicines` VALUES ('527', 'dzm0527', '沉香', '中草药', '中药处方', 'g', '1', 'cx', '1500979678', '1508217525', '');
INSERT INTO `dzm_his_medicines` VALUES ('528', 'dzm0528', '穿破石', '中草药', '中药处方', 'g', '1', 'cps', '1500979678', '1508217525', '');
INSERT INTO `dzm_his_medicines` VALUES ('529', 'dzm0529', '五加皮', '中草药', '中药处方', 'g', '1', 'wjp', '1500979678', '1508377481', '');

-- ----------------------------
-- Table structure for dzm_his_member
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_member`;
CREATE TABLE `dzm_his_member` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_name` varchar(20) DEFAULT NULL COMMENT '登录名',
  `password` varchar(60) DEFAULT NULL COMMENT '登录密码',
  `last_login_time` int(11) unsigned DEFAULT '0' COMMENT '最后一次登录时间',
  `last_login_ip` int(10) DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL COMMENT '注册时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否允许用户登录( 1 是  2否) 3 删除',
  `p_id` int(11) DEFAULT '0' COMMENT '医院id，用于区分用户类型及其医生所属诊所',
  `type` tinyint(2) DEFAULT '1' COMMENT '1,管理员，2，医生，3.护士，4，挂号员，5，收费员6，发药员，7，财务8，其他人员',
  `department_id` int(11) DEFAULT '0' COMMENT '科室id',
  `rank` tinyint(2) DEFAULT '0' COMMENT '医生级别 0:其他  1:主治医师  2:副主任医师  3:主任医师  4:医士  5:医师  6:助理医师  7:实习医师  8:主管护师  9:护师  10:护士  11:医师助理  12:研究生  13:随访员 ',
  `qrid` int(10) unsigned DEFAULT '0' COMMENT '二维码标识',
  `openid` varchar(50) DEFAULT '' COMMENT '用户授权登录openid',
  `money_balance` decimal(10,2) DEFAULT '0.00' COMMENT '用户余额',
  `money_lock` decimal(10,2) DEFAULT '0.00' COMMENT '冻结中余额',
  `recomment_code` varchar(255) DEFAULT '' COMMENT '邀请码',
  `update_time` int(10) DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`uid`),
  KEY `qrid` (`qrid`) USING BTREE,
  KEY `user_name` (`user_name`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `p_id` (`p_id`) USING BTREE,
  KEY `type` (`type`) USING BTREE,
  KEY `department_id` (`department_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='HIS用户表';

-- ----------------------------
-- Records of dzm_his_member
-- ----------------------------
INSERT INTO `dzm_his_member` VALUES ('1', 'dzm', '$2y$12$RdmrtauNU53QpqAss8vM7.nc5/ncg/xZ8eh/3WIFAHTZ5B6rexJQ.', '0', null, '1511947869', '1', '0', '1', '0', '0', '0', '', '0.00', '0.00', '', '0');
INSERT INTO `dzm_his_member` VALUES ('2', '13200010002', '$2y$12$3ISeuAXcEPkc8mBpMB.JpuuoJVWfgd.8V.LCjtYTRl2TCQ0MJhISG', '0', null, '1511949108', '1', '1', '2', '1', '1', '0', '', '0.00', '0.00', '', '0');

-- ----------------------------
-- Table structure for dzm_his_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_operation_log`;
CREATE TABLE `dzm_his_operation_log` (
  `oid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `operation` varchar(64) NOT NULL COMMENT '具体操作',
  `details` varchar(100) NOT NULL,
  `optime` int(10) NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`oid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='诊所系统操作记录表';

-- ----------------------------
-- Records of dzm_his_operation_log
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_patient_credit
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_patient_credit`;
CREATE TABLE `dzm_his_patient_credit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hospital_id` int(10) unsigned DEFAULT '0' COMMENT '医院id',
  `patient_id` int(10) unsigned DEFAULT '0' COMMENT '患者id',
  `doctor_id` int(10) unsigned DEFAULT '0' COMMENT '医生id,若为0，就是通用类型',
  `qa_id` int(10) unsigned DEFAULT '0' COMMENT '来源id，用户提问id',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '使用状态：0，未使用，1已使用',
  `qa_id2` int(10) unsigned DEFAULT '0' COMMENT '使用于哪个问题id',
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  `uptime` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `hospital_id` (`hospital_id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `status` (`status`),
  KEY `qa_id2` (`qa_id2`),
  KEY `qa_id` (`qa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户免费提问额度';

-- ----------------------------
-- Records of dzm_his_patient_credit
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_patient_file
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_patient_file`;
CREATE TABLE `dzm_his_patient_file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `patient_id` int(10) NOT NULL DEFAULT '0' COMMENT '患者id',
  `emergency_contact_name` varchar(50) NOT NULL DEFAULT '' COMMENT '紧急联系人',
  `emergency_contact_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '紧急联系人电话',
  `emergency_contact_relation` tinyint(2) NOT NULL DEFAULT '0' COMMENT '紧急联系人关系 1：爸爸  2：妈妈  3：儿子  4：女儿  5：亲戚  6：朋友',
  `left_ear_hearing` tinyint(1) NOT NULL DEFAULT '0' COMMENT '左耳听力 1：正常  2：耳聋',
  `right_ear_hearing` tinyint(1) NOT NULL DEFAULT '0' COMMENT '右耳听力 1：正常  2：耳聋',
  `left_vision` decimal(10,1) NOT NULL COMMENT '左眼视力',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `right_vision` decimal(10,1) NOT NULL COMMENT '右眼视力',
  `height` decimal(10,1) NOT NULL COMMENT '身高',
  `weight` decimal(10,1) NOT NULL COMMENT '体重',
  `blood_type` text NOT NULL COMMENT '血型 1:A 2:B 3:AB 4:O    Rh血型 1:阴性 2:阳性',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `personal_info` varchar(100) NOT NULL DEFAULT '' COMMENT '个人史',
  `family_info` varchar(100) NOT NULL DEFAULT '' COMMENT '家族史',
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `patient_id` (`patient_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='患者用户档案表';

-- ----------------------------
-- Records of dzm_his_patient_file
-- ----------------------------
INSERT INTO `dzm_his_patient_file` VALUES ('1', '2', '妈', '15500010002', '2', '1', '1', '0.0', '0', '2.0', '185.0', '80.0', '[1,0]', '1511951716', 'aaa', 'bbb');

-- ----------------------------
-- Table structure for dzm_his_prescription_extracharges
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_prescription_extracharges`;
CREATE TABLE `dzm_his_prescription_extracharges` (
  `pre_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(10) NOT NULL DEFAULT '0' COMMENT '添加用户id',
  `hid` int(10) NOT NULL DEFAULT '0' COMMENT '医院id',
  `extracharges_name` varchar(50) NOT NULL DEFAULT '' COMMENT '处方附加费名称',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '处方类型  0:中药处方  1:西药处方',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  UNIQUE KEY `pre_id` (`pre_id`) USING BTREE,
  KEY `extracharges_name` (`extracharges_name`),
  KEY `hid` (`hid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='处方附加费用表';

-- ----------------------------
-- Records of dzm_his_prescription_extracharges
-- ----------------------------
INSERT INTO `dzm_his_prescription_extracharges` VALUES ('1', '1', '1', '煎药', '10.00', '0', '1511948806', '0');
INSERT INTO `dzm_his_prescription_extracharges` VALUES ('2', '1', '1', '分装', '3.00', '1', '1511948832', '0');

-- ----------------------------
-- Table structure for dzm_his_purchase
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_purchase`;
CREATE TABLE `dzm_his_purchase` (
  `purchase_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '采购信息ID',
  `medicines_id` int(10) NOT NULL COMMENT '医院药品关联表：hmr_id',
  `batches_of_inventory_id` int(10) NOT NULL COMMENT '批次库存ID',
  `purchase_num` int(10) NOT NULL COMMENT '采购数量',
  `purchase_unit` varchar(50) NOT NULL COMMENT '采购单位',
  `purchase_trade_price` decimal(10,2) NOT NULL COMMENT '批发价',
  `purchase_prescription_price` decimal(10,2) NOT NULL COMMENT '处方价',
  `purchase_trade_total_amount` decimal(10,2) NOT NULL COMMENT '采购批发总额',
  `purchase_prescription_total_amount` decimal(10,2) NOT NULL COMMENT '采购处方总额',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `hmr_id` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`purchase_id`),
  KEY `medicines_id` (`medicines_id`) USING BTREE,
  KEY `batches_of_inventory_id` (`batches_of_inventory_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='采购信息表';

-- ----------------------------
-- Records of dzm_his_purchase
-- ----------------------------
INSERT INTO `dzm_his_purchase` VALUES ('1', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '40');
INSERT INTO `dzm_his_purchase` VALUES ('2', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '39');
INSERT INTO `dzm_his_purchase` VALUES ('3', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '38');
INSERT INTO `dzm_his_purchase` VALUES ('4', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '37');
INSERT INTO `dzm_his_purchase` VALUES ('5', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '36');
INSERT INTO `dzm_his_purchase` VALUES ('6', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '35');
INSERT INTO `dzm_his_purchase` VALUES ('7', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '34');
INSERT INTO `dzm_his_purchase` VALUES ('8', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '33');
INSERT INTO `dzm_his_purchase` VALUES ('9', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '32');
INSERT INTO `dzm_his_purchase` VALUES ('10', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '31');
INSERT INTO `dzm_his_purchase` VALUES ('11', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '30');
INSERT INTO `dzm_his_purchase` VALUES ('12', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '29');
INSERT INTO `dzm_his_purchase` VALUES ('13', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '28');
INSERT INTO `dzm_his_purchase` VALUES ('14', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '27');
INSERT INTO `dzm_his_purchase` VALUES ('15', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '26');
INSERT INTO `dzm_his_purchase` VALUES ('16', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '25');
INSERT INTO `dzm_his_purchase` VALUES ('17', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '24');
INSERT INTO `dzm_his_purchase` VALUES ('18', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '23');
INSERT INTO `dzm_his_purchase` VALUES ('19', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '22');
INSERT INTO `dzm_his_purchase` VALUES ('20', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '21');
INSERT INTO `dzm_his_purchase` VALUES ('21', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '20');
INSERT INTO `dzm_his_purchase` VALUES ('22', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '19');
INSERT INTO `dzm_his_purchase` VALUES ('23', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '18');
INSERT INTO `dzm_his_purchase` VALUES ('24', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '17');
INSERT INTO `dzm_his_purchase` VALUES ('25', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '16');
INSERT INTO `dzm_his_purchase` VALUES ('26', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '15');
INSERT INTO `dzm_his_purchase` VALUES ('27', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '14');
INSERT INTO `dzm_his_purchase` VALUES ('28', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '13');
INSERT INTO `dzm_his_purchase` VALUES ('29', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '12');
INSERT INTO `dzm_his_purchase` VALUES ('30', '0', '1', '9999', 'g', '15.00', '47.00', '149985.00', '469953.00', '1511949342', '11');
INSERT INTO `dzm_his_purchase` VALUES ('31', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '40');
INSERT INTO `dzm_his_purchase` VALUES ('32', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '39');
INSERT INTO `dzm_his_purchase` VALUES ('33', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '38');
INSERT INTO `dzm_his_purchase` VALUES ('34', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '37');
INSERT INTO `dzm_his_purchase` VALUES ('35', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '36');
INSERT INTO `dzm_his_purchase` VALUES ('36', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '35');
INSERT INTO `dzm_his_purchase` VALUES ('37', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '34');
INSERT INTO `dzm_his_purchase` VALUES ('38', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '33');
INSERT INTO `dzm_his_purchase` VALUES ('39', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '32');
INSERT INTO `dzm_his_purchase` VALUES ('40', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '31');
INSERT INTO `dzm_his_purchase` VALUES ('41', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '30');
INSERT INTO `dzm_his_purchase` VALUES ('42', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '29');
INSERT INTO `dzm_his_purchase` VALUES ('43', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '28');
INSERT INTO `dzm_his_purchase` VALUES ('44', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '27');
INSERT INTO `dzm_his_purchase` VALUES ('45', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '26');
INSERT INTO `dzm_his_purchase` VALUES ('46', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '25');
INSERT INTO `dzm_his_purchase` VALUES ('47', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '24');
INSERT INTO `dzm_his_purchase` VALUES ('48', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '23');
INSERT INTO `dzm_his_purchase` VALUES ('49', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '22');
INSERT INTO `dzm_his_purchase` VALUES ('50', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '21');
INSERT INTO `dzm_his_purchase` VALUES ('51', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '20');
INSERT INTO `dzm_his_purchase` VALUES ('52', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '19');
INSERT INTO `dzm_his_purchase` VALUES ('53', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '18');
INSERT INTO `dzm_his_purchase` VALUES ('54', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '17');
INSERT INTO `dzm_his_purchase` VALUES ('55', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '16');
INSERT INTO `dzm_his_purchase` VALUES ('56', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '15');
INSERT INTO `dzm_his_purchase` VALUES ('57', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '14');
INSERT INTO `dzm_his_purchase` VALUES ('58', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '13');
INSERT INTO `dzm_his_purchase` VALUES ('59', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '12');
INSERT INTO `dzm_his_purchase` VALUES ('60', '0', '2', '9999', '', '15.00', '47.00', '149985.00', '469953.00', '1511949357', '11');

-- ----------------------------
-- Table structure for dzm_his_registeredfee
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_registeredfee`;
CREATE TABLE `dzm_his_registeredfee` (
  `reg_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL COMMENT '用户id',
  `company_id` int(11) NOT NULL COMMENT '公司ID',
  `registeredfee_name` varchar(255) NOT NULL COMMENT '挂号费用名称',
  `registeredfee_fee` decimal(8,2) unsigned NOT NULL COMMENT '金额',
  `registeredfee_sub_fee` decimal(8,2) unsigned NOT NULL COMMENT '子费用总数',
  `registeredfee_aggregate_amount` decimal(8,2) unsigned NOT NULL COMMENT '挂号费用总金额',
  `numberOfSub` int(5) NOT NULL COMMENT '子费用数量',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`reg_id`),
  KEY `mid` (`mid`) USING BTREE,
  KEY `company_id` (`company_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='挂号费用表';

-- ----------------------------
-- Records of dzm_his_registeredfee
-- ----------------------------
INSERT INTO `dzm_his_registeredfee` VALUES ('1', '1', '1', '急诊', '5.00', '1.00', '6.00', '1', '1512003694');
INSERT INTO `dzm_his_registeredfee` VALUES ('2', '1', '1', '门诊', '5.00', '0.00', '5.00', '0', '1512003683');

-- ----------------------------
-- Table structure for dzm_his_registeredfee_sub
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_registeredfee_sub`;
CREATE TABLE `dzm_his_registeredfee_sub` (
  `reg_sub_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reg_id` int(10) unsigned NOT NULL COMMENT '挂号费用ID',
  `sub_registeredfee_name` varchar(255) NOT NULL COMMENT '挂号费用子名称',
  `sub_registeredfee_fee` decimal(8,2) NOT NULL COMMENT '子费用 ',
  PRIMARY KEY (`reg_sub_id`),
  KEY `reg_id` (`reg_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='挂号费用子表';

-- ----------------------------
-- Records of dzm_his_registeredfee_sub
-- ----------------------------
INSERT INTO `dzm_his_registeredfee_sub` VALUES ('1', '1', '病历本', '1.00');

-- ----------------------------
-- Table structure for dzm_his_registration
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_registration`;
CREATE TABLE `dzm_his_registration` (
  `registration_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) NOT NULL COMMENT '患者ID',
  `physician_id` int(10) NOT NULL COMMENT '医生ID',
  `operator_id` int(10) NOT NULL COMMENT '操作员ID',
  `company_id` int(10) NOT NULL COMMENT '诊所ID',
  `department_id` int(10) NOT NULL COMMENT '科室ID',
  `registeredfee_id` int(10) NOT NULL COMMENT '挂号费用ID',
  `registration_amount` float(8,2) NOT NULL COMMENT '挂号总金额',
  `registration_number` bigint(20) NOT NULL COMMENT '挂号编号',
  `registration_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '挂号状态,1为待就诊，3为已退号，2为已就诊,4为作废，5,为未付款,6，为部分支付',
  `scheduling_id` int(10) NOT NULL COMMENT '排班主表ID',
  `scheduling_subsection_id` int(10) NOT NULL COMMENT '排班时段表ID',
  `scheduling_week_id` int(10) NOT NULL COMMENT '排班星期表ID',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `pkg_id` int(10) unsigned DEFAULT '0' COMMENT '收费总表care_pkg.id',
  PRIMARY KEY (`registration_id`),
  KEY `patient_id` (`patient_id`) USING BTREE,
  KEY `physician_id` (`physician_id`) USING BTREE,
  KEY `operator_id` (`operator_id`) USING BTREE,
  KEY `company_id` (`company_id`) USING BTREE,
  KEY `department_id` (`department_id`) USING BTREE,
  KEY `registeredfee_id` (`registeredfee_id`) USING BTREE,
  KEY `registration_status` (`registration_status`) USING BTREE,
  KEY `scheduling_id` (`scheduling_id`) USING BTREE,
  KEY `pkg_id` (`pkg_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='门诊挂号';

-- ----------------------------
-- Records of dzm_his_registration
-- ----------------------------
INSERT INTO `dzm_his_registration` VALUES ('1', '1', '2', '1', '1', '1', '2', '5.00', '201711290000100001', '1', '1', '1', '7', '1511950076', '0', '1');
INSERT INTO `dzm_his_registration` VALUES ('2', '3', '2', '1', '1', '1', '1', '6.00', '201711300000100002', '5', '1', '1', '5', '1512004052', '0', '3');

-- ----------------------------
-- Table structure for dzm_his_scheduling
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_scheduling`;
CREATE TABLE `dzm_his_scheduling` (
  `scheduling_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `physicianid` int(10) NOT NULL COMMENT '医生ID',
  `department_id` int(10) NOT NULL COMMENT '科室ID',
  `company_id` int(10) NOT NULL COMMENT '诊所ID',
  `start_time_this_week` varchar(50) NOT NULL COMMENT '本周开始时间',
  `end_time_this_week` varchar(50) NOT NULL COMMENT '本周结束时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`scheduling_id`),
  KEY `physicianid` (`physicianid`) USING BTREE,
  KEY `department_id` (`department_id`) USING BTREE,
  KEY `company_id` (`company_id`) USING BTREE,
  KEY `start_time_this_week` (`start_time_this_week`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='我的排班';

-- ----------------------------
-- Records of dzm_his_scheduling
-- ----------------------------
INSERT INTO `dzm_his_scheduling` VALUES ('1', '2', '1', '1', '2017-11-27', '2017-12-03', '1511949395', '0');

-- ----------------------------
-- Table structure for dzm_his_scheduling_subsection
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_scheduling_subsection`;
CREATE TABLE `dzm_his_scheduling_subsection` (
  `scheduling_subsection_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subsection_type` int(2) NOT NULL COMMENT '每天的时段：上午：1；下午：2；晚上：3；',
  `scheduling_id` int(10) NOT NULL COMMENT '排班id',
  PRIMARY KEY (`scheduling_subsection_id`),
  KEY `subsection_type` (`subsection_type`) USING BTREE,
  KEY `scheduling_id` (`scheduling_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='排班时段表';

-- ----------------------------
-- Records of dzm_his_scheduling_subsection
-- ----------------------------
INSERT INTO `dzm_his_scheduling_subsection` VALUES ('1', '1', '1');
INSERT INTO `dzm_his_scheduling_subsection` VALUES ('2', '2', '1');
INSERT INTO `dzm_his_scheduling_subsection` VALUES ('3', '3', '1');

-- ----------------------------
-- Table structure for dzm_his_scheduling_week
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_scheduling_week`;
CREATE TABLE `dzm_his_scheduling_week` (
  `scheduling_week_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(255) NOT NULL COMMENT '时间',
  `week` int(2) NOT NULL COMMENT '星期一：1；星期二：2；星期三：3；星期四：4；星期五：5；星期六：6；星期日：0',
  `registeredfee_id` int(10) DEFAULT NULL COMMENT '挂号费用ID',
  `scheduling_subsection_id` int(10) unsigned NOT NULL COMMENT '排班分段ID',
  PRIMARY KEY (`scheduling_week_id`),
  KEY `date` (`date`) USING BTREE,
  KEY `week` (`week`) USING BTREE,
  KEY `registeredfee_id` (`registeredfee_id`) USING BTREE,
  KEY `scheduling_subsection_id` (`scheduling_subsection_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='排班星期表';

-- ----------------------------
-- Records of dzm_his_scheduling_week
-- ----------------------------
INSERT INTO `dzm_his_scheduling_week` VALUES ('1', '2017/11/27', '1', '1', '1');
INSERT INTO `dzm_his_scheduling_week` VALUES ('2', '2017/11/28', '2', '1', '1');
INSERT INTO `dzm_his_scheduling_week` VALUES ('3', '2017/11/29', '3', '1', '1');
INSERT INTO `dzm_his_scheduling_week` VALUES ('4', '2017/11/30', '4', '2', '1');
INSERT INTO `dzm_his_scheduling_week` VALUES ('5', '2017/12/01', '5', '1', '1');
INSERT INTO `dzm_his_scheduling_week` VALUES ('6', '2017/12/02', '6', '2', '1');
INSERT INTO `dzm_his_scheduling_week` VALUES ('7', '2017/12/03', '0', '2', '1');
INSERT INTO `dzm_his_scheduling_week` VALUES ('8', '2017/11/27', '1', '2', '2');
INSERT INTO `dzm_his_scheduling_week` VALUES ('9', '2017/11/28', '2', '2', '2');
INSERT INTO `dzm_his_scheduling_week` VALUES ('10', '2017/11/29', '3', '2', '2');
INSERT INTO `dzm_his_scheduling_week` VALUES ('11', '2017/11/30', '4', '1', '2');
INSERT INTO `dzm_his_scheduling_week` VALUES ('12', '2017/12/01', '5', '1', '2');
INSERT INTO `dzm_his_scheduling_week` VALUES ('13', '2017/12/02', '6', '2', '2');
INSERT INTO `dzm_his_scheduling_week` VALUES ('14', '2017/12/03', '0', '2', '2');
INSERT INTO `dzm_his_scheduling_week` VALUES ('15', '2017/11/27', '1', '2', '3');
INSERT INTO `dzm_his_scheduling_week` VALUES ('16', '2017/11/28', '2', '1', '3');
INSERT INTO `dzm_his_scheduling_week` VALUES ('17', '2017/11/29', '3', '1', '3');
INSERT INTO `dzm_his_scheduling_week` VALUES ('18', '2017/11/30', '4', '1', '3');
INSERT INTO `dzm_his_scheduling_week` VALUES ('19', '2017/12/01', '5', '1', '3');
INSERT INTO `dzm_his_scheduling_week` VALUES ('20', '2017/12/02', '6', '2', '3');
INSERT INTO `dzm_his_scheduling_week` VALUES ('21', '2017/12/03', '0', '2', '3');

-- ----------------------------
-- Table structure for dzm_his_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_sms_log`;
CREATE TABLE `dzm_his_sms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `mobile` varchar(11) DEFAULT '' COMMENT '手机号',
  `add_time` int(11) DEFAULT '0' COMMENT '发送时间',
  `code` varchar(10) DEFAULT '' COMMENT '验证码',
  `status` smallint(2) DEFAULT '1' COMMENT '1.发送成功2发送失败',
  `type` tinyint(4) DEFAULT '1' COMMENT '1注册验证码，2，其他',
  `error_info` varchar(255) DEFAULT '' COMMENT '发送失败的错误信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='短信发送记录表';

-- ----------------------------
-- Records of dzm_his_sms_log
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_storage_log
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_storage_log`;
CREATE TABLE `dzm_his_storage_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '更改价格表',
  `company_id` int(10) NOT NULL COMMENT '诊所ID',
  `purchase_id` int(10) NOT NULL COMMENT '采购信息ID',
  `batches_of_inventory_number` bigint(20) NOT NULL COMMENT '批次库存编号',
  `medicines_id` int(10) NOT NULL COMMENT '药品ID',
  `modifier_id` int(10) NOT NULL COMMENT '修改人ID',
  `new_quantity` int(10) NOT NULL,
  `new_trade_price` decimal(10,2) NOT NULL COMMENT '新批发价',
  `new_prescription_price` decimal(10,2) NOT NULL COMMENT '新处方价',
  `old_quantity` int(10) NOT NULL COMMENT '原数量',
  `old_trade_price` decimal(10,2) NOT NULL COMMENT '原批发价',
  `old_prescription_price` decimal(10,2) NOT NULL COMMENT '原处方价',
  `operation_module` tinyint(3) NOT NULL COMMENT '操作模块；采购：1，审核：2',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='入库操作log日志表';

-- ----------------------------
-- Records of dzm_his_storage_log
-- ----------------------------
INSERT INTO `dzm_his_storage_log` VALUES ('1', '1', '1', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('2', '1', '2', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('3', '1', '3', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('4', '1', '4', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('5', '1', '5', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('6', '1', '6', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('7', '1', '7', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('8', '1', '8', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('9', '1', '9', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('10', '1', '10', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('11', '1', '11', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('12', '1', '12', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('13', '1', '13', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('14', '1', '14', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('15', '1', '15', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('16', '1', '16', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('17', '1', '17', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('18', '1', '18', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('19', '1', '19', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('20', '1', '20', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('21', '1', '21', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('22', '1', '22', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('23', '1', '23', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('24', '1', '24', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('25', '1', '25', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('26', '1', '26', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('27', '1', '27', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('28', '1', '28', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('29', '1', '29', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('30', '1', '30', '201711290000100001', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949342');
INSERT INTO `dzm_his_storage_log` VALUES ('31', '1', '31', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('32', '1', '32', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('33', '1', '33', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('34', '1', '34', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('35', '1', '35', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('36', '1', '36', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('37', '1', '37', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('38', '1', '38', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('39', '1', '39', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('40', '1', '40', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('41', '1', '41', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('42', '1', '42', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('43', '1', '43', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('44', '1', '44', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('45', '1', '45', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('46', '1', '46', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('47', '1', '47', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('48', '1', '48', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('49', '1', '49', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('50', '1', '50', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('51', '1', '51', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('52', '1', '52', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('53', '1', '53', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('54', '1', '54', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('55', '1', '55', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('56', '1', '56', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('57', '1', '57', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('58', '1', '58', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('59', '1', '59', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');
INSERT INTO `dzm_his_storage_log` VALUES ('60', '1', '60', '201711290000100002', '0', '1', '9999', '15.00', '47.00', '9999', '15.00', '47.00', '1', '1511949357');

-- ----------------------------
-- Table structure for dzm_his_supplier
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_supplier`;
CREATE TABLE `dzm_his_supplier` (
  `sid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `supplier_name` varchar(100) NOT NULL DEFAULT '' COMMENT '供应商名称',
  `contact_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系人名称',
  `contact_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '联系人手机',
  `contact_telephone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人电话',
  `bank_account` varchar(50) NOT NULL DEFAULT '' COMMENT '银行账号',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '供应商地址',
  `hospital_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '医院id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`sid`),
  KEY `hospital_id` (`hospital_id`) USING BTREE,
  KEY `update_time` (`update_time`) USING BTREE,
  KEY `supplier_name` (`supplier_name`),
  KEY `contact_name` (`contact_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='供应商表';

-- ----------------------------
-- Records of dzm_his_supplier
-- ----------------------------
INSERT INTO `dzm_his_supplier` VALUES ('1', '供应商A', '供应商A联系人', '15100010002', '010-00010002', '622021706004742103', '供应商A地址', '1', '1511949274', '0');

-- ----------------------------
-- Table structure for dzm_his_transaction_record
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_transaction_record`;
CREATE TABLE `dzm_his_transaction_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `operator_id` int(11) DEFAULT NULL,
  `hospital_id` int(10) unsigned DEFAULT '0' COMMENT '医院id',
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '用户ID',
  `type_id` tinyint(1) unsigned DEFAULT '0' COMMENT '收支类型：0进，1出',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '交易金额',
  `money_balance` decimal(10,2) DEFAULT '0.00' COMMENT '余额',
  `money_lock` decimal(10,2) DEFAULT '0.00' COMMENT '冻结中金额',
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '交易时间',
  `ip` varchar(32) DEFAULT NULL COMMENT '交易IP',
  `memo` varchar(128) DEFAULT NULL COMMENT '事由',
  `pkg_id` int(10) unsigned DEFAULT '0' COMMENT '相关订单id',
  PRIMARY KEY (`id`),
  KEY `operator_id` (`operator_id`),
  KEY `hospital_id` (`hospital_id`),
  KEY `user_id` (`user_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户收支记录';

-- ----------------------------
-- Records of dzm_his_transaction_record
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_work_log
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_work_log`;
CREATE TABLE `dzm_his_work_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tab_name` varchar(32) DEFAULT '' COMMENT '关联表名',
  `rel_id` int(10) unsigned DEFAULT '0' COMMENT '操作相关id',
  `title` varchar(128) DEFAULT NULL COMMENT '操作说明',
  `addtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '操作时间',
  `session` varchar(255) DEFAULT NULL COMMENT '操作者session',
  `cookie` varchar(255) DEFAULT NULL COMMENT '操作者cookie',
  `ip` varchar(32) DEFAULT NULL COMMENT '操作IP',
  `dev_info` varchar(255) DEFAULT NULL COMMENT '开发信息',
  PRIMARY KEY (`id`),
  KEY `tab_name` (`tab_name`),
  KEY `rel_id` (`rel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='重要操作记录';

-- ----------------------------
-- Records of dzm_his_work_log
-- ----------------------------
INSERT INTO `dzm_his_work_log` VALUES ('1', 'his_care_pkg', '2', '订单支付', '2017-11-29 18:27:34', '{\"hospital_info\":{\"uid\":\"1\",\"id\":\"1\",\"userid\":\"1\",\"appid\":null,\"appsecret\":null,\"token\":null,\"encodingaeskey\":null,\"access_token\":null,\"access_token_expires\":\"0\",\"jsapi_ticket\":null,\"jsapi_ticket_expires\":\"0\",\"mchid\":null,\"mchkey\":null,\"ssl_cert_path\":nul', '{\"DZMADMIN\":\"3hoh6jl1oapnhuiv6udnnk8e30\",\"his_think_language\":\"zh-CN\"}', '127.0.0.1', 'filename:DoctorController.class.php,class:His\\Controller\\DoctorController,method:His\\Controller\\DoctorController::payOrder');

-- ----------------------------
-- Table structure for dzm_his_wxmp
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_wxmp`;
CREATE TABLE `dzm_his_wxmp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned DEFAULT '0' COMMENT '所属用户',
  `appid` varchar(20) DEFAULT NULL COMMENT '公众平台appid',
  `appsecret` varchar(40) DEFAULT NULL COMMENT '公众平台appsecret',
  `token` varchar(255) DEFAULT NULL COMMENT '微信后台填写的TOKEN,自动回复',
  `encodingaeskey` varchar(255) DEFAULT NULL COMMENT '微信后台填写的EncodingAESKey，自动回复',
  `access_token` text,
  `access_token_expires` int(10) unsigned DEFAULT '0' COMMENT '过期时间，php时间戳',
  `jsapi_ticket` text COMMENT 'jsapi_ticket',
  `jsapi_ticket_expires` int(10) DEFAULT '0' COMMENT 'jsapi_ticket过期时间',
  `mchid` varchar(32) DEFAULT NULL COMMENT '微信支付企业帐号',
  `mchkey` varchar(64) DEFAULT NULL COMMENT '微信支付key',
  `ssl_cert_path` varchar(255) DEFAULT NULL COMMENT '微信企业付款证书部分路径',
  `app_id` varchar(32) DEFAULT NULL COMMENT '支付宝app_id',
  `merchant_private_key` text COMMENT '支付宝商户私钥，您的原始格式RSA私钥',
  `alipay_public_key` text COMMENT '支付宝公钥',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `appid` (`appid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='微信公众平台信息';

-- ----------------------------
-- Records of dzm_his_wxmp
-- ----------------------------
INSERT INTO `dzm_his_wxmp` VALUES ('1', '1', null, null, null, null, null, '0', null, '0', null, null, null, null, null, null);

-- ----------------------------
-- Table structure for dzm_his_wxopenid
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_wxopenid`;
CREATE TABLE `dzm_his_wxopenid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appid` varchar(20) DEFAULT NULL COMMENT 'appid预留分表用',
  `openid` varchar(32) DEFAULT NULL COMMENT '微信openid',
  `userid` int(10) unsigned DEFAULT NULL COMMENT '用户id',
  `usertype` tinyint(1) unsigned DEFAULT '0' COMMENT '用户类型，0系统管理员，1诊所医院，2医生，3患者',
  `addtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`),
  KEY `userid` (`userid`),
  KEY `appid` (`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='绑定微信openid和用户id的关系';

-- ----------------------------
-- Records of dzm_his_wxopenid
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_wxopenid_cache
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_wxopenid_cache`;
CREATE TABLE `dzm_his_wxopenid_cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appid` varchar(32) DEFAULT NULL COMMENT '微信appid',
  `openid` varchar(64) DEFAULT NULL COMMENT '微信openid',
  `url` varchar(255) DEFAULT NULL COMMENT 'openid获取成功后跳转的url',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='openid跨域名获取';

-- ----------------------------
-- Records of dzm_his_wxopenid_cache
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_wxqr
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_wxqr`;
CREATE TABLE `dzm_his_wxqr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hospital_id` int(10) unsigned DEFAULT '0' COMMENT '医院id',
  `qr_id` int(10) unsigned DEFAULT '0' COMMENT '微信永久二维码标识1-100000，每个微信公众平台10万个',
  `url` varchar(255) DEFAULT NULL COMMENT '微信二维码内容',
  `appid` varchar(32) DEFAULT NULL,
  `userid` int(10) unsigned DEFAULT '0' COMMENT '绑定用户id',
  PRIMARY KEY (`id`),
  KEY `hospital_id` (`hospital_id`),
  KEY `qr_id` (`qr_id`),
  KEY `appid` (`appid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='保存微信永久二维码及用户关系';

-- ----------------------------
-- Records of dzm_his_wxqr
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_wxqrlogin
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_wxqrlogin`;
CREATE TABLE `dzm_his_wxqrlogin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `enuid` varchar(64) DEFAULT NULL COMMENT '加密的用户id',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态，0等待，1已扫，2完成',
  `createdate` date DEFAULT NULL,
  `openid` varchar(32) DEFAULT NULL COMMENT '微信openid',
  PRIMARY KEY (`id`),
  KEY `createdate` (`createdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信扫码登录';

-- ----------------------------
-- Records of dzm_his_wxqrlogin
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_his_wx_menu
-- ----------------------------
DROP TABLE IF EXISTS `dzm_his_wx_menu`;
CREATE TABLE `dzm_his_wx_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hospital_id` int(10) unsigned DEFAULT '0' COMMENT '医院id',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '主菜单ID',
  `type` varchar(32) DEFAULT NULL COMMENT '菜单的响应动作类型',
  `name` varchar(64) DEFAULT NULL COMMENT '菜单标题，不超过16个字节，子菜单不超过60个字节',
  `key` varchar(128) DEFAULT NULL COMMENT '菜单KEY值，用于消息接口推送，不超过128字节',
  `url` varchar(255) DEFAULT NULL COMMENT '网页链接，用户点击菜单可打开链接，不超过1024字节。',
  `media_id` varchar(255) DEFAULT NULL COMMENT '调用新增永久素材接口返回的合法media_id',
  `appid` varchar(32) DEFAULT NULL,
  `pagepath` varchar(255) DEFAULT NULL COMMENT '小程序的页面路径',
  `listorder` int(10) unsigned DEFAULT '0' COMMENT '排序 ASC',
  PRIMARY KEY (`id`),
  KEY `hospital_id` (`hospital_id`),
  KEY `pid` (`pid`),
  KEY `listorder` (`listorder`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='保存微信公众平台菜单';

-- ----------------------------
-- Records of dzm_his_wx_menu
-- ----------------------------

-- ----------------------------
-- Table structure for dzm_patient
-- ----------------------------
DROP TABLE IF EXISTS `dzm_patient`;
CREATE TABLE `dzm_patient` (
  `patient_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `hospital_id` int(10) DEFAULT '0' COMMENT '所属医院、诊所',
  `name` varchar(50) NOT NULL DEFAULT '',
  `openid` varchar(80) DEFAULT '0' COMMENT '微信openid',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '患者电话',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `password` varchar(60) DEFAULT '' COMMENT ' 登录密码',
  `sex` tinyint(2) DEFAULT '0' COMMENT '患者性别1男2女',
  `birthday` varchar(50) DEFAULT NULL,
  `id_card` char(18) DEFAULT NULL,
  `mobile1` char(11) DEFAULT NULL,
  `is_final` tinyint(4) DEFAULT '0' COMMENT '是否完善信息，0否1已完善',
  `last_login_ip` int(10) DEFAULT '0' COMMENT '最后登录ip',
  `last_login_time` int(10) DEFAULT '0' COMMENT '最后登录时间',
  `address` varchar(120) DEFAULT NULL COMMENT '地址信息',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `province_id` int(11) DEFAULT '0' COMMENT '省区id',
  `city_id` int(11) DEFAULT '0' COMMENT '市区id',
  `district_id` int(11) DEFAULT '0' COMMENT '县区id',
  `allergy_info` varchar(100) DEFAULT NULL COMMENT '过敏信息',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否移除 0：正常 1：删除',
  PRIMARY KEY (`patient_id`),
  KEY `dzm_patient_hostpital_id_index` (`hospital_id`),
  KEY `dzm_patient_last_login_time_index` (`last_login_time`),
  KEY `name` (`name`),
  KEY `tel` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='患者用户表';

-- ----------------------------
-- Records of dzm_patient
-- ----------------------------
INSERT INTO `dzm_patient` VALUES ('1', '1', '患者A', '0', '15800010002', '0', '$2y$12$1EyI5lPXFbYkD9iwD8mCq.9Gslx0Ru.J7LcBHCs9nhAVwkrJkQWlq', '0', '2003-10-28', '', null, '0', '0', '0', '患者A住址', '1511950076', '0', '0', '0', null, '0');
INSERT INTO `dzm_patient` VALUES ('2', '1', '李四', '0', '15200010002', '1511951249', '', '1', '1996-10-29', '110001190001011580', null, '0', '0', '0', '李四住址', '1511951716', '0', '0', '0', '无', '0');
INSERT INTO `dzm_patient` VALUES ('3', '1', '王五', '0', '18800010002', '0', '$2y$12$nnhgMdzbQB5za/psZOmz9uxTwUcttKKtDz95Mq1jb97evSAK8Wjg.', '0', '2013-11-01', '', null, '0', '0', '0', '王五住址', '1512004052', '0', '0', '0', null, '0');
