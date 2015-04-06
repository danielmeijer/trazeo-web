<?php
    namespace Trazeo\MyPageBundle\Classes;

    use Symfony\Component\HttpFoundation\Request;
    use Trazeo\MyPageBundle\Entity\Module;

    class ModuleAbstract {
        private function parse_classname ($name)
        {
            return array(
                'namespace' => array_slice(explode('\\', $name), 0, -1),
                'classname' => join('', array_slice(explode('\\', $name), -1)),
            );
        }

        /**
         * Prepara la visualización del form
         * Devuelve las variables necesarias para el módulo
         * [Listo para sobreescribir]
         *
         * @param $container
         * @param Module $module
         * @return null
         */
        function prepareFront($container, Module $module = null) {
            return null;
        }

        /**
         * Ejecuta una acción de un módulo
         * Se llama desde un Controlador genérico
         * [Listo para sobreescribir]
         *
         * @param $container
         * @return null
         */
        function executeAction($container, Module $module, Request $request) {
            return null;
        }

        /**
         * Hace render del Twig por defecto para la parte Front
         *
         * @param $container
         * @param $module
         * @return mixed
         */
        function renderFront($container, Module $module = null) {
            $vars = $this->prepareFront($container, $module);
            return $container->renderView('TrazeoMyPageBundle:Module:'.$this->getClassName().'.html.twig', array('module' => $module, 'vars' => $vars));
        }

        public function getClassName() {
            $info = $this->parse_classname(get_class($this));
            $classname = $info['classname'];
            return $classname;
        }

        public function getStyle(Module $module) {
            return "";
        }

        public function getContentByPart(Module $module, $int_part = 0) {
            $parts = explode("|", $module->getContent());
            if (isset($parts[$int_part])) {
                return $parts[$int_part];
            } else {
                return null;
            }
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