<?php
/**
 * Created by PhpStorm.
 * User: New User
 * Date: 10/19/2018
 * Time: 4:37 PM
 */

namespace Drupal\group_entity\Plugin\GroupContentEnabler;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

class GroupEntityDeriver extends DeriverBase {

  /**
   * {@inheritdoc}.
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $entity_types = [ // @todo: Make this array configurable
      'media' => 'Drupal\media\Entity\MediaType', // this key is found in the annotation for the entity_type, bundle_of
      'block_content' => 'Drupal\block_content\Entity\BlockContentType',
    ];
    foreach ($entity_types as $type_id => $type) {

      if (class_exists($type)) {
        /**
         * @var $name string
         * @var $bundle ConfigEntityBundleBase
         */
        foreach ($type::loadMultiple() as $name => $bundle) {
          $label = $bundle->label();
          $this->derivatives[$type_id.'-'.$name] = [
              'entity_type_id' => $type_id,
              'entity_bundle' => $name,
              'label' => t('Group @type (@bundle)', ['@type' => $type_id, '@bundle' => $label]),
              'description' => t('Adds %type - %bundle content to groups both publicly and privately.', ['%type' => $type_id, '%bundle' => $label]),
            ] + $base_plugin_definition;
        }
      }
    }

    return $this->derivatives;
  }
}