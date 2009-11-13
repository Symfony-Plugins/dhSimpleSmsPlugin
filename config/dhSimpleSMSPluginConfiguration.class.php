<?php
class dhSimpleSMSPluginConfiguration extends sfPluginConfiguration {
    public function initialize() {

        if(sfConfig::get('sf_debug')) {
            //$this->dispatcher->connect('view.cache.filter_content', array('fsWebDebugPanelCache', 'decorateContentWithDebug'));
        }

        //$this->filterTestFiles($event, $files)

//        if (sfConfig::get('app_sf_simplesms__register', true) && in_array('sfGuardAuth', sfConfig::get('sf_enabled_modules', array()))) {
//            $this->dispatcher->connect('routing.load_configuration', array('sfGuardRouting', 'listenToRoutingLoadConfigurationEvent'));
//        }


    }
}