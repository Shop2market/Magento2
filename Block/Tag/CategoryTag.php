<?php

namespace Adcurve\Adcurve\Block\Tag;

class CategoryTag extends \Magento\Catalog\Block\Product\ListProduct
{
    public $configHelper;
    public $tagHelper;

    protected $categoryResource;

    protected $categoryInfo;
    protected $_productCollection;
    protected $_reg;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Adcurve\Adcurve\Helper\Tag $tagHelper,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->tagHelper = $tagHelper;
        $this->categoryResource = $categoryResource;
        $this->_reg = $registry;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    /**
     * Get current category from registry
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->_reg->registry('current_category');
    }

    /**
     * Set all required information about category
     *
     * @return void
     */
    public function setCategoryInfo()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $path = $this->getCurrentCategory()->getPath();
        $categoryIds = explode('/', $path);
        array_shift($categoryIds); // Shift off Magento root category
        array_shift($categoryIds); // Shift off Default Website root category
        $this->categoryInfo = [];
        $i = 0;
        foreach ($categoryIds as $categoryId) {
            $categoryName = $this->categoryResource->getAttributeRawValue($categoryId, 'name', $storeId);
            $this->categoryInfo[$i] = $categoryName;
            $i++;
        }
    }

    /**
     * Get category path, except Magento root and website root
     *
     * @return string
     */
    public function getCategoryPath()
    {
        if (!$this->categoryInfo) {
            $this->setCategoryInfo();
        }

        return implode('/', $this->categoryInfo);
    }

    /**
     * Get parent category name if applicable
     *
     * @return string
     */
    public function getParentCategoryName()
    {
        if (!$this->categoryInfo) {
            $this->setCategoryInfo();
        }

        if (empty($this->categoryInfo)) {
            return '';
        }

        if (count($this->categoryInfo) > 1) {
            $workingArray = $this->categoryInfo;
            array_pop($workingArray);
            return end($workingArray);
        }

        return end($this->categoryInfo);
    }

    /**
     * Get current category name
     *
     * @return string
     */
    public function getCategoryName()
    {
        if (!$this->categoryInfo) {
            $this->setCategoryInfo();
        }

        if (empty($this->categoryInfo)) {
            return '';
        }

        return end($this->categoryInfo);
    }
}
