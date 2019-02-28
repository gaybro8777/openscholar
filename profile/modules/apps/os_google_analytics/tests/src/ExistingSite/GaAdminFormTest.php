<?php

namespace Drupal\Tests\os_google_analytics\ExistingSite;

use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Class PublicationsFormTest.
 *
 * @group functional
 *
 * @package Drupal\Tests\os_publications\ExistingSite
 */
class GaAdminFormTest extends ExistingSiteBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->user = $this->createUser([], '', TRUE);
    $this->simpleUser = $this->createUser();
  }

  /**
   * Test Setting form route.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testGaSettingsPath() {
    $this->drupalLogin($this->user);

    $this->drupalGet('cp/settings/analytics');
    drupal_flush_all_caches();
    $this->drupalGet('cp/settings/analytics');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Test Settings form.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testGaSettingsForm() {
    $this->drupalLogin($this->user);
    $this->drupalGet('cp/settings/analytics');
    drupal_flush_all_caches();
    $this->drupalGet('cp/settings/analytics');
    // Testing multiple form fields.
    $edit = [
      'edit-web-property-id' => 'UA-1234567-A1',
    ];
    $this->submitForm($edit, 'edit-submit');
    $this->assertSession()->fieldValueEquals('edit-web-property-id', 'UA-1234567-A1');
  }

}
