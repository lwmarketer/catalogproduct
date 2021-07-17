<?php

namespace Lovevox\CatalogProduct\Block\Adminhtml\Product\Helper\Form;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Registry;

class Gallery extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * Media directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var here you set your ui form
     */
    protected $formName = 'product_form';

    protected $_filePath = 'media/catalog/product';

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Gallery constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param \Magento\Framework\Data\Form $form
     * @param array $data
     * @param DataPersistorInterface|null $dataPersistor
     * @throws FileSystemException
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Registry $registry,
        \Magento\Framework\Data\Form $form,
        $data = [],
        DataPersistorInterface $dataPersistor = null
    )
    {
        parent::__construct($context, $storeManager, $registry, $form, $data, $dataPersistor);
        $this->productRepository = $productRepository;
        $this->dataPersistor = $dataPersistor ?: ObjectManager::getInstance()->get(DataPersistorInterface::class);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }

    /**
     * Get product images
     *
     * @return array|null
     */
    public function getImages()
    {
        $catalog_banner_images = $this->getProductGalleryImages();
        $data = [];
        $images = json_decode($catalog_banner_images, true);
        if ($images) {
            foreach ($images as $key => $image) {
                try {
                    $fileHandler = $this->mediaDirectory->stat('catalog/product' . $image);
                    $size = $fileHandler['size'];
                } catch (FileSystemException $e) {
                    $size = 0;
                }

                $data[] = [
                    'position' => $key,
                    'file' => $image,
                    'media_type' => 'image',
                    'value_id' => $key,
                    'size' => $size,
                    'url' => $this->storeManager->getStore()->getBaseUrl() . $this->_filePath . $image
                ];
            }
        }

        return json_encode($data);
    }

    /**
     * @return ProductInterface|Product|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductGalleryImages()
    {
        $product = $this->registry->registry('current_product');
        if ($product->getId()) {
            $product = $this->productRepository->getById($product->getId());
            return $product->getData('product_gallery_images') ?? null;
        }
        return null;
    }
}
