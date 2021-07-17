<?php

namespace Lovevox\CatalogProduct\Block;

use Magento\Framework\Data\Collection;
use Magento\Framework\View\Element\Template\Context;
use Magento\Reports\Block\Product\Viewed;
use Magento\Catalog\Model\ProductFactory;

class View extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    protected $_filePath = 'pub/media/catalog/product';
    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }


    /**
     * @return mixed
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * @param $product_id
     * @return \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollection
     */
    public function getImageGalleryList()
    {
        $data = [];
        $product = $this->getProduct();
        $product_gallery_images = $product->getData('product_gallery_images') ?: null;
        $images = json_decode($product_gallery_images, true);
        if ($images) {
            foreach ($images as $key => $image) {
                $data[] = $this->storeManager->getStore()->getBaseUrl() . $this->_filePath . $image;
            }
        }
        return $data;
    }
}

