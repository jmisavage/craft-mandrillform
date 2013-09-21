<?php
namespace Craft;

class MandrillFormPlugin extends BasePlugin
{
	function getName()
	{
		return Craft::t('MandrillForm');
	}

	function getVersion()
	{
		return '0.6';
	}

	function getDeveloper()
	{
		return 'Jeremy Misavage';
	}

	function getDeveloperUrl()
	{
		return 'http://idlehamster.com';
	}

	protected function defineSettings()
	{
		return array(
			'emailRecipient' => array(AttributeType::Email, 'required' => true),
			'apiKey' => array(AttributeType::String, 'required' => true),
			'emailSubject' => array(AttributeType::String),
		);
	}

	public function getSettingsHtml()
	{
		return craft()->templates->render('mandrillform/_settings', array(
			'settings' => $this->getSettings()
		));
	}
}
