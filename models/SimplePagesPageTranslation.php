<?php

class SimplePagesPageTranslation extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $page_id;
    public $locale;
    public $title;
    public $text;

    public function getPage()
    {
        return $this->getTable('SimplePagesPage')->find($this->page_id);
    }

    public function getResourceId()
    {
        return 'SimplePages_PageTranslation';
    }

    public function getRecordUrl($action = 'show')
    {
        if ('show' == $action) {
            return public_url($this->slug);
        }

        return array(
            'module' => 'simple-pages',
            'controller' => 'translation',
            'action' => $action,
            'id' => $this->id,
        );
    }

    protected function _validate()
    {
        if (empty($this->title)) {
            $this->addError('title', __('The page must be given a title.'));
        }

        if (255 < strlen($this->title)) {
            $this->addError('title', __('The title for your page must be 255 characters or less.'));
        }

        if (!$this->fieldIsUnique('locale')) {
            $this->addError('locale', __('There is already a translation for this language.'));
        }
    }
}
