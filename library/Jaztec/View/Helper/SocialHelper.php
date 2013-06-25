<?php

class Jaztec_View_Helper_SocialHelper {
	
	public function socialHelper($url) {
		$html = '<div><div style="float:right;width:120px;">';
		$html .= $this->_buildFacebookScript($url) . '</div><div style="float:right;width:120px;">';
		$html .= $this->_buildLinkedinScript($url);
		$html .= '</div></div>';
		return $html;
	}
	
	/**
	 * 
	 * @param string $url
	 * @return string $facebookScript
	 */
	protected function _buildFacebookScript($url) {
		return '<iframe src="http://www.facebook.com/plugins/like.php?href=' . $url . '&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=true&amp;action=recommend&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px;" allowTransparency="true"></iframe>';
	}
	
	/**
	 * 
	 * @param string $url
	 * @return string $linkedinScript
	 */
	protected function _buildLinkedinScript($url) {
		return '<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="' . $url . '"></script>';
	}
}