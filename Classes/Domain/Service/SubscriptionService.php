<?php

namespace KaufmannDigital\CleverReach\Domain\Service;

use GuzzleHttp\Psr7\ServerRequest;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Validation\ValidatorResolver;
use Neos\Flow\Annotations as Flow;

/**
 * Class SubscriptionService
 *
 * @package KaufmannDigital\CleverReach\Domain\Service
 */
class SubscriptionService
{

    /**
     * @Flow\Inject
     * @var CleverReachApiService
     */
    protected $apiService;


    /**
     * @Flow\Inject
     * @var ValidatorResolver
     */
    protected $validatorResolver;


    /**
     * @param array $receiverData
     * @param NodeInterface $registrationForm
     * @param ServerRequest|null $httpRequest
     */
    public function create(array $receiverData, NodeInterface $registrationForm, ServerRequest $httpRequest = null)
    {
        $groupId = $registrationForm->getProperty('groupId');
        $formId = $registrationForm->getProperty('formId');
        $useDOI = $registrationForm->getProperty('useDOI');

        //Add user to list
        $this->apiService->addReceiver($receiverData, $groupId, !$useDOI);


        //Send confirmation mail (if Doi activated)
        if ($useDOI === true) {
            $doiData = [
                'user_ip' => $httpRequest->getAttribute('clientIpAddress'),
                'referer' => $httpRequest->getHeader('Referer'),
                'user_agent' => $httpRequest->getHeader('User-Agent')
            ];

            $this->apiService->sendDoubleOptInMail($receiverData['email'], $groupId, $formId, $doiData);

        }

    }

}
