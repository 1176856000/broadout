<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Controller\Adminhtml\Affiliate;

use Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface;
use Plumrocket\Affiliate\Api\Data\AffiliateProgramInterface;

class Save extends \Plumrocket\Affiliate\Controller\Adminhtml\Affiliate
{
    const FILE_EXTENSION_FAIL = 12343;

    /**
     * Date for setUpdatedAt() and setCreatedAt()
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $dateTime;

    /**
     * Filesystem Factory
     * @var \Magento\Framework\FilesystemFactory
     */
    public $filesystemFactory;

    /**
     * File Uploader Factory
     * @var \Magento\MediaStorage\Model\File\UploaderFactoryFactory
     */
    public $uploaderFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                             $context
     * @param \Plumrocket\Affiliate\Model\AffiliateManager                    $affiliateManager
     * @param \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider
     * @param \Magento\Framework\FilesystemFactory                            $filesystemFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                     $dateTime
     * @param \Magento\MediaStorage\Model\File\UploaderFactoryFactory         $uploaderFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\Affiliate\Model\AffiliateManager $affiliateManager,
        AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider,
        \Magento\Framework\FilesystemFactory $filesystemFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\MediaStorage\Model\File\UploaderFactoryFactory $uploaderFactory
    ) {
        parent::__construct($context, $affiliateManager, $affiliateProgramTypeProvider);
        $this->dateTime             = $dateTime;
        $this->filesystemFactory    = $filesystemFactory;
        $this->uploaderFactory      = $uploaderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function _saveAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/*'));
        }
        $model = $this->_getModel();

        try {

            $data = $request->getParams();
            $date = $this->dateTime->gmtDate();

            if (count($this->getRequest()->getFiles())) {
                $aMediaDName = 'praffiliate';
                $mediaDirectory = $this->filesystemFactory->create()
                    ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                foreach ($model->getPageSections() as $section) {
                    $fileLable = 'section_'.$section['key'].'_library';
                    try {
                        $uploader = $this->uploaderFactory->create();
                        $uploader = $uploader->create(['fileId' => $fileLable]);
                        $uploader->setAllowedExtensions(['js']);
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(true);
                        $uploader->setAllowCreateFolders(true);
                        $result = $uploader->save(
                            $mediaDirectory->getAbsolutePath($aMediaDName)
                        );
                        $data[$fileLable] = $aMediaDName . $result['file'];
                    } catch (\Exception $e) {
                        if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                            throw new \Exception($e->getMessage());
                        }
                    }
                }
            }

            if (! $model instanceof AffiliateProgramInterface) {
                foreach ($model->getPageSections() as $section) {
                    $fileDeleteLable = 'section_'.$section['key'].'_library_delete';
                    $fileLable = 'section_'.$section['key'].'_library';

                    if (isset($data[$fileDeleteLable]) && $model->getData($fileLable)) {
                        $data[$fileLable] = '';
                        $mediaDirectory->delete($model->getData($fileLable));
                    }
                }
            }

            if ($request->getParam('additional_data')) {
                $model->setAdditionalDataValues($request->getParam('additional_data'));
                unset($data['additional_data']);

            }

            if ($request->getParam('stores')) {
                $model->setStores($request->getParam('stores'));
                unset($data['stores']);
            }

            $model->addData($data)
                ->setUpdatedAt($date);

            if (!$model->getId()) {
                $model->setCreatedAt($date);
            }

            $model->save();

            $this->messageManager->addSuccess(__($this->_objectTitle.' has been saved.'));
            $this->_setFormData(false);

            if ($request->getParam('back')) {
                $this->_redirect('*/*/edit', [$this->_idKey => $model->getId()]);
            } else {
                $this->_redirect('*/*');
            }
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_setFormData();
        } catch (\Exception $e) {
            if ($e->getCode() == self::FILE_EXTENSION_FAIL) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            } else {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving this '.strtolower($this->_objectTitle))
                );
            }
            $this->_setFormData();
        }

        $this->_forward('edit');
    }
}
