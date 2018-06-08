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

        $this->displayName = $this->trans('New Offer');
        $this->description = $this->trans('Adds an alert with a popular product');

        $this->ps_versions_compliancy = array('min' => '1.7.2.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:blockreassurance/views/templates/hook/mark_newoffer.tpl';
    }

    public function install()
    {
        return parent::install()
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayHome')
        && Configuration::updateValue('background', '#000')
        && Configuration::updateValue('font_color', '#fff' )
        && Configuration::updateValue('animation', 'bounce' )
        && Configuration::updateValue('productid', '1' );
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
        $background = Configuration::get('background');
        $font_color = Configuration::get('font_color');
        $animation = Configuration::get('animation');
        $productid = Configuration::get('productid');
        $this ->context->smarty-> assign('background',$background);
        $this ->context->smarty-> assign('font_color',$font_color);
        $this ->context->smarty-> assign('animation',$animation);
        $this ->context->smarty-> assign('productid',$productid);
        //
        $image = Image::getCover($productid);
        $product = new Product($productid, false, Context::getContext()->language->id);
       $link = new Link;//because getImageLInk is not static function
       $imagePath = "http://". $link->getImageLink($product->link_rewrite, $image['id_image'], 'home_default');
       $link_product=$link -> getProductLink($product);       
       $price= number_format($product->price, 2, '.', ',');     
       $this ->context->smarty-> assign(
         array('product' =>  $product->name, 'img' => $imagePath , 'price' => $price, 'link' =>$link_product));
        //
       return $this->display(__FILE__, 'views/templates/hook/alert.tpl');
   }

   public function getContent()
   {
       $output = null;

       if (Tools::isSubmit('submit'.$this->name))
       {
        $background= strval(Tools::getValue('background'));
        $font_color= strval(Tools::getValue('font_color'));
        $animation= strval(Tools::getValue('animation'));
        $productid= strval(Tools::getValue('productid'));
        if ( (!$background || empty($background) || !Validate::isGenericName($background))
           &&   (!$font_color || empty($font_color)  || !Validate::isGenericName($font_color)) )
            $output .= $this->displayError($this->l('Invalid Configuration value'));

        else
        {
            Configuration::updateValue('font_color', $font_color);
            Configuration::updateValue('background', $background);
            Configuration::updateValue('animation', $animation);
            Configuration::updateValue('productid', $productid);
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
                'type' => 'color',
                'label' => $this->l('Background'),
                'name' => 'background',
                'data-hex' => true,
                'class'   => 'mColorPicker',
                'desc' => $this->l('Enter hex code.'),
                'required' => true
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Font color'),
                'name' => 'font_color',
                'data-hex' => true,
                'class'   => 'mColorPicker',
                'desc' => $this->l('Enter hex code.'),
                'required' => true
            ),

            // Select animation 1
            array(
              'type' => 'select',
              'label' => $this->l('Animation:'),
              'name' => 'animation',
              'desc' => $this->l('Select Animation.'),
              'required' => true,
              'options' => array(
                 'query' => $idanimation = array( 

                    array(
                        'idanimation' => 'bounce',
                        'name' => 'bounce'
                    ),
                    array(
                        'idanimation' => 'flash',
                        'name' => 'flash'
                    ), 
                    array(
                        'idanimation' => 'pulse',
                        'name' => 'pulse'
                    ),                                       
                ),
                 'id' => 'idanimation',
                 'name' => 'name'
             )
          ),
        )        ,
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
    $helper->fields_value['font_color'] = Configuration::get('font_color');
    $helper->fields_value['animation'] = $idanimation;
    $helper->fields_value['productid'] = Configuration::get('productid');


    return $helper->generateForm($fields_form);
}


}
