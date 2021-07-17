<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lovevox\CatalogProduct\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Save;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Employ additional authorization logic when a category is saved.
 */
class ProductMediaGallery implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Psr\Log\LoggerInterface $logger */
            $logger = $objectManager->get('\Psr\Log\LoggerInterface');

            /** @var ProductInterface $product */
            $product = $observer->getEvent()->getData('product');
            if ($product) {
                /** @var Save $controller */
                $controller = $observer->getEvent()->getController();
                $galleries = $controller->getRequest()->getParam('productgallery');
                if ($galleries) {
                    $data = [];
                    foreach ($galleries['images'] as $gallery) {
                        if ($gallery['removed'] != 1 && !empty($gallery['file'])) {
                            $data[$gallery['position']] = $gallery['file'];
                        }
                    }
                    ksort($data);
                    $product->setData('product_gallery_images', json_encode($data));
                    $product->save();
                    $logger->info("execute ==> info: " . json_encode($product->getData()));
                }
            }
        }catch (\Exception $exception){
            $logger->info("ProductMediaGallery error ==> info: " . $exception->getMessage());
        }
    }
}
