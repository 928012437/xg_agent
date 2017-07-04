<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

define('XG_AGENT_DEBUG', false);
!defined('XG_AGENT_PATH') && define('XG_AGENT_PATH', IA_ROOT . '/addons/xg_agent/');
!defined('XG_AGENT_CORE') && define('XG_AGENT_CORE', XG_AGENT_PATH . 'core/');
!defined('XG_AGENT_DATA') && define('XG_AGENT_DATA', XG_AGENT_PATH . 'data/');
!defined('XG_AGENT_VENDOR') && define('XG_AGENT_VENDOR', XG_AGENT_PATH . 'vendor/');
!defined('XG_AGENT_CORE_WEB') && define('XG_AGENT_CORE_WEB', XG_AGENT_CORE . 'web/');
!defined('XG_AGENT_CORE_MOBILE') && define('XG_AGENT_CORE_MOBILE', XG_AGENT_CORE . 'mobile/');
!defined('XG_AGENT_CORE_SYSTEM') && define('XG_AGENT_CORE_SYSTEM', XG_AGENT_CORE . 'system/');
!defined('XG_AGENT_PLUGIN') && define('XG_AGENT_PLUGIN', XG_AGENT_PATH . 'plugin/');
!defined('XG_AGENT_PROCESSOR') && define('XG_AGENT_PROCESSOR', XG_AGENT_CORE . 'processor/');
!defined('XG_AGENT_INC') && define('XG_AGENT_INC', XG_AGENT_CORE . 'inc/');
!defined('XG_AGENT_URL') && define('XG_AGENT_URL', $_W['siteroot'] . 'addons/xg_agent/');
!defined('XG_AGENT_TASK_URL') && define('XG_AGENT_TASK_URL', $_W['siteroot'] . 'addons/xg_agent/core/task/');
!defined('XG_AGENT_LOCAL') && define('XG_AGENT_LOCAL', '../addons/xg_agent/');
!defined('XG_AGENT_STATIC') && define('XG_AGENT_STATIC', XG_AGENT_URL . 'static/');
!defined('XG_AGENT_PREFIX') && define('XG_AGENT_PREFIX', 'xg_agent_');
define('XG_AGENT_PLACEHOLDER', '../addons/xg_agent/static/images/placeholder.png');