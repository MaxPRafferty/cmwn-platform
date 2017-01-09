<?php


namespace Api\V1\Rest\Flag;

use Flag\Flag;
use Flag\Service\FlagServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use User\UserInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class FlagResource
 * @package Api\V1\Rest\Flag
 */
class FlagResource extends AbstractResourceListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;
    /**
     * @var FlagServiceInterface
     */
    protected $flagService;

    /**
     * FlagResource constructor.
     * @param FlagServiceInterface $flagService
     */
    public function __construct($flagService)
    {
        $this->flagService = $flagService;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $flaggedImages = $this->flagService->fetchAll(null, new FlagEntity());
        return new FlagCollection($flaggedImages);
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $flagger = $this->getAuthenticationService()->getIdentity();

        $inputData = $this->getInputFilter()->getValues();
        $flag = new Flag($inputData);
        $flag->setFlagger($flagger);
        $this->flagService->saveFlag($flag);
        return new FlagEntity($flag->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function fetch($flagId)
    {
        $flag = $this->flagService->fetchFlag($flagId, new FlagEntity());
        return new FlagEntity($flag->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function update($flagId, $data)
    {
        $flagger = $this->getAuthenticationService()->getIdentity();
        $flagger = $flagger instanceof UserInterface ? $flagger->getUserId() : $flagger;

        $flag = $this->fetch($flagId);
        $data = $this->getInputFilter()->getValues();
        $data['flagger'] = $flagger;
        $saveFlag = new Flag(array_merge($flag->getArrayCopy(), $data));
        $this->flagService->updateFlag($saveFlag);
    }

    /**
     * @inheritdoc
     */
    public function delete($flagId)
    {
        $flag = $this->fetch($flagId);
        $this->flagService->deleteFlag($flag);
        return new ApiProblem(200, 'Flag deleted', 'Ok');
    }
}
