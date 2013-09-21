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
			// Include Mandrill API SDK
			require_once(CRAFT_PLUGINS_PATH.'mandrillform/vendor/Mandrill.php');		
		
			$api = new \Mandrill($settings->apiKey);

			// construct model
			$formData = new MandrillFormModel();
			$formData->fromEmail = craft()->request->getPost('fromEmail');
			$formData->fromName = craft()->request->getPost('fromName', '');
			$formData->subject = $settings->emailSubject;
			$formData->message = craft()->request->getPost('message');

			// Mandrill status responses
			$messageStatus = 'unknown';
			$rejectReason = null;
			
			if ($formData->validate())
			{
				// Configure Mandrill Message Object
				$message = array(
					'text'			=> $formData->message,
					'subject'		=> $formData->subject,
					'from_email'	=> $formData->fromEmail,
					'from_name'		=> $formData->fromName,
					'to'			=> array(
						array(
							'email'	=> $settings->emailRecipient
						)
					)
				);

				try
				{
					// Mandrip API Message Send(settings, async, ...)
					$result = $api->messages->send($message, false);

					// craft messages/variables
					$messageStatus = $result[0]['status'];
					$rejectReason = $result[0]['reject_reason'];

					craft()->userSession->setNotice('Your message has been ' . $messageStatus);

					if (($successRedirectUrl = craft()->request->getPost('successRedirectUrl', null)) != null)
					{
						$this->redirect($successRedirectUrl);
					}
					else
					{
						$this->redirectToPostedUrl();
					}
				}
				catch (\Mandrill_Error $e)
				{
					Craft::log('A mandrill error occured: ' . get_class($e) . ' - ' . $e->getMessage(), LogLevel::Error);
					craft()->userSession->setError(Craft::t('Couldn’t send email. Check your settings.'));
					$this->redirectToPostedUrl();
				}
			}

			craft()->urlManager->setRouteVariables(array(
				'message'		=> $formData,
				'status'		=> $messageStatus,
				'rejectReason'	=> $rejectReason
			));
		}
	}

}
