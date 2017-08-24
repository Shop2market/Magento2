<?php
namespace Adcurve\Adcurve\Model;

class AdminUser
{
    private $roleFactory;
    private $rulesFactory;
	private $userFactory;
	private $_encryptor;
	
    /**
     * Constructor
     *
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\Authorization\Model\RulesFactory $rulesFactory
     */
    public function __construct(
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Encryption\Encryptor $encrypor
        )
    {
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
		$this->userFactory = $userFactory;
		$this->_encryptor = $encrypor;
    }
 
    /**
     * Create Adcurve Api role
	 * 
	 * @return boolean $success
     */
    public function createAdcurveRole()
    {
        $role = $this->roleFactory->create();
		
		$role->load('Adcurve', 'role_name');
		if($role->getId()){
			return false;
		}
		
        $role->setName('Adcurve')
            ->setPid(0)
            ->setRoleType(\Magento\Authorization\Model\Acl\Role\Group::ROLE_TYPE)
            ->setUserType(\Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN);
        $role->save();
		
		// @TODO: Add all applicable rights that are needed for Adcurve
        $resource = [
        	//'Magento_Backend::admin',
            'Magento_Sales::sales',
            'Magento_Sales::create',
            'Magento_Sales::actions_view',
            'Magento_Sales::cancel'
        ];
		
        $this->rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();
		return true;
    }
	
	/**
     * Create Adcurve Admin user with Adcurve Api role and returns password
	 * 
	 * @return array(boolean $created, string $password)
     */
	public function createAdcurveUser()
	{
		$user = $this->userFactory->create();
		
		$user->load('Adcurve', 'username');
		if($user->getId()){
			return ['created' => false, 'password' => false];
		}
		
		$password = bin2hex(openssl_random_pseudo_bytes(32));
		
		$user->setFirstname('Adcurve')
			->setLastname('Integration')
			->setEmail('info@Adcurve.nl')
			->setUsername('Adcurve')
			->setPassword($password);
		$user->save();
		
		return ['created' => true, 'password' => $password];
	}
}