<?php
namespace Craft;

class MandrillFormController extends BaseController
{
	protected $allowAnonymous = true;

	public function actionSendMessage()
	{
		$this->requirePostRequest();

		$plugin = craft()->plugins->getPlugin('mandrillform');

		// We need the plugin before we can do anything
		if (!$plugin)
		{
			throw new Exception('Couldn’t find the MandrillForm plugin!');
		}

		// Check the plugin's settings to see if we have everything
		$settings = $plugin->getSettings();

		if (($toEmail = $settings->emailRecipient) == null)
		{
			craft()->userSession->setError('The "To Email" address is not set on the plugin’s settings page.');
			Craft::log('Tried to send a contact form request, but missing the "To Email" address on the plugin’s settings page.', LogLevel::Error);
			$this->redirectToPostedUrl();
		}
		elseif (($apiKey = $settings->apiKey) == null)
		{
			craft()->userSession->setError('The "API key" is not set on the plugin’s settings page.');
			Craft::log('Tried to send a contact form request, but missing the "API key" on the plugin’s settings page.', LogLevel::Error);
			$this->redirectToPostedUrl();
		}
		else
		{
			// include mandrill api class
			require_once(CRAFT_PLUGINS_PATH.'mandrillform/vendor/Mandrill.php');		
		
			$api = new \Mandrill($settings->apiKey);
			
			// construct api call
			$message = array(
				'text'			=> craft()->request->getPost('message'),
				'subject'		=> $settings->emailSubject,
				'from_email'	=> craft()->request->getPost('fromEmail'),
				'from_name'		=> craft()->request->getPost('fromName'),
				'to'			=> array(
					array(
						'email'	=> $settings->emailRecipient
					)
				)
			);
			
			$async = false;
			$result = $api->messages->send($message, $async);
			
			// Handle mandrill response			
			craft()->userSession->setFlash('status', $result[0]['status']);
			craft()->userSession->setError($result[0]['reject_reason']);
			
			if (($successRedirectUrl = craft()->request->getPost('successRedirectUrl', null)) != null)
			{
				$this->redirect($successRedirectUrl);
			}
			else
			{
				$this->redirectToPostedUrl();
			}
		}
	}
	
}
