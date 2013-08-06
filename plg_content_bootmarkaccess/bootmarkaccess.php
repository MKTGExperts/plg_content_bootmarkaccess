<?php defined('_JEXEC') or die;
/**
 * @copyright   Copyright (C) 2013 mktgexperts.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see http://www.gnu.org/licenses/gpl-2.0.html
 */



defined('JPATH_BASE') or die;

/**
 * Main plugin class.
 *
 */
class plgContentBootMarkAccess extends JPlugin {

	protected $_regex;
	protected $_pluginTag;

	public function __construct($subject, $config){
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->_pluginTag = $this->params->get("plugin_tag", "bootmarkaccess");
		$pluginTag = $this->_pluginTag;
		$this->_regex = "#{".$pluginTag."\s(.*?)}(.*?){/".$pluginTag."}#s";
	}

	public function onContentPrepare($context, $article, $params, $page = 0)
	{
		return $this->onPrepareContent($article, $params, $page);
	}
	public function onPrepareContent($article, $params, $limitstart = 0){
		$article->text = preg_replace_callback($this->_regex, array($this,"getMatches"), $article->text);
		return true;
	}

	/**
	 * @param	string	The RSS Item object.
	 *
	 * @return	void
	 */
	public function onPrepareBootmarkAccessRSSFeedRow($item){
		$item->description = preg_replace_callback($this->_regex,array($this,"getMatches"), $item->description);
		return true;
	}

	/**
	* Replaces the matched tags
	*
	* @param array	An array of matches (see preg_match_all)
	*
	* @return string
	*/
	public function getMatches($matches){
		$groups		= JFactory::getUser()->groups;
		$chunck		= $matches[2];
		$temp		= explode("{access_denied_message}", $chunck);
		$output		= $temp[0];
		$msg		= $temp[1];
		preg_match_all('/\d+/i', $matches[1], $gids);
		// check f user belong to one of the groups
		foreach ($gids[0] as $gid){
			if (in_array($gid, $groups)) return $output;
		}
		// if set return access denied message
		return $msg;
	}
}






