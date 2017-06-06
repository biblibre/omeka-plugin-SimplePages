<?php

class SimplePages_TranslationController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('SimplePagesPageTranslation');
    }

    public function addAction()
    {
        $translation = new SimplePagesPageTranslation;

        $translation->page_id = $this->getParam('page_id');
        $form = $this->_getForm($translation);
        $this->view->form = $form;
        $this->_processForm($translation, $form, 'add');
    }

    public function editAction()
    {
        $translation = $this->_helper->db->findById();
        $form = $this->_getForm($translation);
        $this->view->form = $form;
        $this->_processForm($translation, $form, 'edit');
    }

    protected function _getForm($translation)
    {
        $formOptions = array('type' => 'simple_pages_page_translation');
        if ($translation && $translation->exists()) {
            $formOptions['record'] = $translation;
        }

        $form = new Omeka_Form_Admin($formOptions);

        $form->addElementToEditGroup(
            'select', 'locale',
            array(
                'id' => 'simple-pages-locale',
                'value' => $translation->locale,
                'multiOptions' => $this->_getLocaleOptions(),
                'label' => __('Locale'),
                'required' => true,
            )
        );
        $form->addElementToEditGroup(
            'text', 'title',
            array(
                'id' => 'simple-pages-title',
                'value' => $translation->title,
                'label' => __('Title'),
                'description' => __('Name and heading for the page (required)'),
                'required' => true,
            )
        );

        $form->addElementToEditGroup(
            'textarea', 'text',
            array('id' => 'simple-pages-text',
                'cols'  => 50,
                'rows'  => 25,
                'value' => $translation->text,
                'label' => __('Text'),
                'description' => __(
                    'Add content for page. This field supports shortcodes. For a list of available shortcodes, refer to the <a target=_blank href="http://omeka.org/codex/Shortcodes">Omeka Codex</a>.'
                )
            )
        );

        if (class_exists('Omeka_Form_Element_SessionCsrfToken')) {
            $form->addElement('sessionCsrfToken', 'csrf_token');
        }

        return $form;
    }

    /**
     * Process the page translation add and edit forms.
     */
    private function _processForm($translation, $form, $action)
    {
        $this->view->simple_pages_page_translation = $translation;

        if ($this->getRequest()->isPost()) {
            if (!$form->isValid($_POST)) {
                $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
                return;
            }
            try {
                $translation->setPostData($_POST);
                if ($translation->save()) {
                    if ('add' == $action) {
                        $this->_helper->flashMessenger(__('The translation for "%s" has been added.', $translation->locale), 'success');
                    } else if ('edit' == $action) {
                        $this->_helper->flashMessenger(__('The translation for "%s" has been edited.', $translation->locale), 'success');
                    }

                    $this->_helper->redirector('translate', 'index', 'simple-pages', array('id' => $translation->page_id));
                    return;
                }
            } catch (Omeka_Validate_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }
    }

    protected function _getLocaleOptions()
    {
        $localeOptions = array('' => '');

        $localeSwitcherActive = plugin_is_active('LocaleSwitcher');
        $files = scandir(BASE_DIR . '/application/languages');
        foreach ($files as $file) {
            if (strpos($file, '.mo') !== false) {
                $code = str_replace('.mo', '', $file);
                $description = $localeSwitcherActive ? (locale_description($code) . " ($code)") : $code;
                $localeOptions[$code] = $description;
            }
        }
        asort($localeOptions);

        return $localeOptions;
    }

    protected function _getDeleteSuccessMessage($record)
    {
        return __('The translation for "%s" has been deleted.', $record->locale);
    }

    protected function _redirectAfterDelete($record)
    {
                    $this->_helper->redirector('translate', 'index', 'simple-pages', array('id' => $record->page_id));
    }
}
