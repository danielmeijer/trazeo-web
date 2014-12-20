<?php
    namespace Trazeo\MyPageBundle\Classes;

    class ModuleAbstract {
        private function parse_classname ($name)
        {
            return array(
                'namespace' => array_slice(explode('\\', $name), 0, -1),
                'classname' => join('', array_slice(explode('\\', $name), -1)),
            );
        }

        /**
         * Hace render del Twig por defecto para la parte Front
         *
         * @param $container
         * @param $module
         * @return mixed
         */
        function renderFront($container, $module = null) {
            return $container->renderView('TrazeoMyPageBundle:Module:'.$this->getClassName().'.html.twig', array('module' => $module));
        }

        public function getClassName() {
            $info = $this->parse_classname(get_class($this));
            $classname = $info['classname'];
            return $classname;
        }

        public function getStyle() {
            return "";
        }

        public function getClassCSS() {
            return "module-" . $this->getClassName();
        }

        /**
         * Hace render del Twig por defecto para la parte Admin
         * TODO: Aún en construcción.
         *
         * @param $container
         * @param $module
         * @return string
         */
        function renderAdmin($container, $module) {
            $html = "<legend>";
            $html .= $module->getTitle()."</legend>";
            return $html;
        }
    }
?>