<?php
if (!defined('_PS_VERSION_')) {
    exit;
}


class mark_newoffer extends Module
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'mark_newoffer';
        $this->author = 'Arón Yáñez';
        $this->version = '1.0.0';
        $this->tab = 'front_office_features';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Nueva Oferta');
        $this->description = $this->trans('Adds an information block aimed at offering helpful information to reassure customers that your store is trustworthy.', array(), 'Modules.Blockreassurance.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.2.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:blockreassurance/views/templates/hook/mark_newoffer.tpl';
    }

    public function install()
    {
        return parent::install()
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayHome') ;
    }


    public function uninstall()
    {
        return parent::uninstall();
    }


    public function hookDisplayHeader($params)
    {

        $this->context->controller->registerStylesheet('modules-mark_newoffer-animate', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css', ['server' => 'remote', 'position' => 'head','media' => 'all', 'priority' => 160]);
        $this->context->controller->registerStylesheet('modules-mark_newoffer-font', 'https://fonts.googleapis.com/css?family=Anton', ['server' => 'remote', 'position' => 'head','media' => 'all', 'priority' => 161]);

        $this->context->controller->registerStylesheet('modules-mark_newoffer-icon', 'https://use.fontawesome.com/releases/v5.0.13/css/all.css', ['server' => 'remote', 'position' => 'head','media' => 'all', 'priority' => 162]);

        $this->context->controller->registerStylesheet('modules-mark_newoffer-style', 'modules/'.$this->name.'/views/css/style.css', 
            ['media' => 'all', 'priority' => 163]);
//scripts

        $this->context->controller->addjQuery();
        $this->context->controller->registerJavascript('modules-miPrimerModulo', 'modules/'.$this->name.'/views/js/script.js',[ 'position' => 'bottom','priority' => 150]);

        $this->context->controller->registerJavascript('modules-miPrimerModulo', 'modules/'.$this->name.'/views/js/script.js',[ 'position' => 'bottom','priority' => 150]);


    }

    public function hookDisplayHome()
    {
        $value = Configuration::get('background');
        $this ->context->smarty-> assign('value',$value);
        return $this->display(__FILE__, 'views/templates/hook/alert.tpl');
    }

    public function getContent()
    {
       $output = null;

       if (Tools::isSubmit('submit'.$this->name))
       {
        $background= strval(Tools::getValue('background'));
        if (!$background
          || empty($background)
          || !Validate::isGenericName($background))
            $output .= $this->displayError($this->l('Invalid Configuration value'));
            else
            {
                Configuration::updateValue('background', $background);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output.$this->displayForm();
    }


    public function displayForm()
    {
    // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

    // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Background'),
                    'name' => 'background',
                    'size' => 20,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();

    // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

    // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

    // Title and toolbar
        $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit'.$this->name;
    $helper->toolbar_btn = array(
        'save' =>
        array(
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
            '&token='.Tools::getAdminTokenLite('AdminModules'),
        ),
        'back' => array(
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to list')
        )
    );

    // Load current value
    $helper->fields_value['background'] = Configuration::get('background');

    return $helper->generateForm($fields_form);
}

}
