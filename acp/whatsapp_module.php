<?php
/**
*
* @package phpBB Extension - tas2580 Whatsapp Notifier
* @copyright (c) 2015 tas2580 (https://tas2580.net)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tas2580\whatsapp\acp;

use WhatsProt;

class whatsapp_module
{
    var $u_action;

    public function main($id, $mode)
    {
	global $config, $user, $template, $request, $phpbb_root_path, $phpEx;

	$user->add_lang_ext('tas2580/whatsapp', 'common');
	$this->tpl_name = 'acp_whatsapp_body';
	$this->page_title = $user->lang('ACP_WHATSAPP_TITLE');

	add_form_key('acp_whatsapp');

	// Form is submitted
	if($request->is_set_post('submit'))
	{
		if (!check_form_key('acp_whatsapp'))
		{
			trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$config->set('whatsapp_sender', $request->variable('sender', ''));
		$config->set('whatsapp_password', $request->variable('password', ''));
		$config->set('whatsapp_status', $request->variable('status', ''));

		//Update Whatsapp status
		require($phpbb_root_path . 'ext/tas2580/whatsapp/vendor/mgp25/whatsapi/src/whatsprot.class.' . $phpEx);

		$wa = new WhatsProt($config['whatsapp_sender'], '');

		$wa->connect();
		$wa->loginWithPassword($config['whatsapp_password']);
		$wa->sendStatusUpdate($config['whatsapp_status']);

		if($request->is_set_post('image'))
		{
			include_once($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
			$upload = new \fileupload('', array('jpg', 'jpeg', 'gif', 'png'));
			$file =  $upload->form_upload('image');
			if($file->filename)
			{
				$wa->sendSetProfilePicture($file->filename);
			}
		}

		trigger_error($user->lang('ACP_SAVED') . adm_back_link($this->u_action));
	}

	$template->assign_vars(array(
		'U_ACTION'		=> $this->u_action,
		'SENDER'			=> isset($config['whatsapp_sender']) ? $config['whatsapp_sender'] : '',
		'PASSWORD'		=> isset($config['whatsapp_password']) ? $config['whatsapp_password'] : '',
		'STATUS'			=> isset($config['whatsapp_status']) ? $config['whatsapp_status'] : '',
	));
    }
}
