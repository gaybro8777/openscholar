<?php

namespace Drupal\Tests\vsite\Unit;

use Drupal\Core\Language\LanguageInterface;
use Drupal\purl\Modifier;
use Drupal\Tests\UnitTestCase;
use Drupal\vsite\Path\VsiteAliasStorage;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @group vsite
 * @coversDefaultClass \Drupal\vsite\Path\VsiteAliasStorage
 * @codeCoverageIgnore
 *
 *   Tests for the VsitePathActivator class
 */
class VsiteAliasStorageTest extends UnitTestCase {

  /**
   * @var \Symfony\Component\DependencyInjection\ContainerBuilder
   *   Dependency Injection container
   */
  protected $container;

  /**
   * @var \Drupal\vsite\Path\VsiteAliasStorage
   *   The object to test
   */
  protected $vsiteAliasStorage;

  /**
   * @var \PHPUnit_Framework_MockObject_MockObject
   *   Mock for EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \PHPUnit_Framework_MockObject_MockObject
   *   The AliasStorage our tested class is wrapping
   */
  protected $innerAliasStorage;

  /**
   * Set up all the needed mock classes for these tests.
   */
  public function setUp() {
    parent::setUp();

    $this->container = new ContainerBuilder();
    \Drupal::setContainer($this->container);

    $this->innerAliasStorage = $this->createMock('\Drupal\Core\Path\AliasStorageInterface');

    $mockProvider = $this->createMock('\Drupal\purl\Entity\Provider');

    $modifierIndex = $this->createMock('\Drupal\purl\Plugin\ModifierIndex');
    $method = $this->createMock('\Drupal\purl\Plugin\Purl\Method\MethodInterface');
    $modifierIndex->method('getProviderModifiers')
      ->willReturn([
        new Modifier('site01', 1, $method, $mockProvider),
      ]);

    $purlStorage = $this->createMock('\Drupal\Core\Entity\EntityStorageInterface');
    $purlStorage->method('load')
      ->willReturn($mockProvider);

    $this->entityTypeManager = $this->createMock('\Drupal\Core\Entity\EntityTypeManagerInterface');
    $this->entityTypeManager->method('getStorage')
      ->with('purl_provider')
      ->willReturn($purlStorage);

    $group = $this->createMock('\Drupal\group\Entity\GroupInterface');
    $group->method('id')
      ->willReturn(1);

    $vsiteContextManager = $this->createMock('\Drupal\vsite\Plugin\VsiteContextManager');
    $vsiteContextManager->method('getActiveVsite')
      ->willReturn($group);

    $this->vsiteAliasStorage = new VsiteAliasStorage(
      $this->innerAliasStorage, $modifierIndex, $this->entityTypeManager, $vsiteContextManager);
  }

  /**
   * Testing the aliasExists method.
   */
  public function testAliasExists() {
    $lang = LanguageInterface::LANGCODE_SITE_DEFAULT;
    $this->innerAliasStorage->expects($this->once())
      ->method('aliasExists')
      ->with('/[vsite:1]/foo', $lang, NULL)
      ->willReturn(TRUE);

    $this->assertEquals(TRUE, $this->vsiteAliasStorage->aliasExists('/site01/foo', $lang));
  }

  /**
   * Testing the lookupPathSource method.
   */
  public function testLookupSource() {
    $this->innerAliasStorage->expects($this->once())
      ->method('lookupPathSource')
      ->with('/[vsite:1]/foo', LanguageInterface::LANGCODE_SITE_DEFAULT)
      ->willReturn('/node/1');

    $this->assertEquals('/node/1', $this->vsiteAliasStorage->lookupPathSource('/foo', LanguageInterface::LANGCODE_SITE_DEFAULT));
  }

  /**
   * Testing the lookupPathAlias method.
   */
  public function testLookupAlias() {
    $this->innerAliasStorage->expects($this->once())
      ->method('lookupPathAlias')
      ->with('/node/1', LanguageInterface::LANGCODE_SITE_DEFAULT)
      ->willReturn('/[vsite:1]/foo');

    $this->assertEquals('/site01/foo', $this->vsiteAliasStorage->lookupPathAlias('/node/1', LanguageInterface::LANGCODE_SITE_DEFAULT));
  }

  /**
   * Testing alias save functionality.
   */
  public function testSaveAlias() {
    $this->innerAliasStorage->expects($this->once())
      ->method('save')
      ->with()
      ->willReturn([
        'source' => '/node/1',
        'alias' => '/[vsite:1]/foo',
        'langcode' => LanguageInterface::LANGCODE_SITE_DEFAULT,
        'pid' => 1,
      ]);

    $expected = [
      'source' => '/node/1',
      'alias' => '/site01/foo',
      'langcode' => LanguageInterface::LANGCODE_SITE_DEFAULT,
      'pid' => 1,
    ];
    $actual = $this->vsiteAliasStorage->save('/node/1', '/site01/foo', LanguageInterface::LANGCODE_SITE_DEFAULT);
    $this->assertArrayEquals($expected, $actual);
  }

  /**
   * Test group alias save functionality.
   */
  public function testSaveAliasGroup() {
    $this->innerAliasStorage->expects($this->once())
      ->method('save')
      ->with()
      ->willReturn([
        'source' => '/group/1',
        'alias' => '/group/1',
        'langcode' => LanguageInterface::LANGCODE_SITE_DEFAULT,
        'pid' => 1,
      ]);

    $expected = [
      'source' => '/group/1',
      'alias' => '/group/1',
      'langcode' => LanguageInterface::LANGCODE_SITE_DEFAULT,
      'pid' => 1,
    ];
    $actual = $this->vsiteAliasStorage->save('/group/1', '/group/1', LanguageInterface::LANGCODE_SITE_DEFAULT);
    $this->assertArrayEquals($expected, $actual);
  }

  /**
   * Tests vsite storage load.
   */
  public function testLoad() {
    $this->vsiteAliasStorage->save('/node/1', '/site01/foo', LanguageInterface::LANGCODE_SITE_DEFAULT);

    $expected = [
      'source' => '/node/1',
      'alias' => '/site01/foo',
      'langcode' => LanguageInterface::LANGCODE_SITE_DEFAULT,
      'pid' => 1,
    ];

    $this->assertArrayEquals($expected, $this->vsiteAliasStorage->load([
      'alias' => '/site01/foo',
    ]));
  }

  /**
   * Tests vsite storage delete.
   */
  public function testDelete() {
    $this->vsiteAliasStorage->save('/node/1', '/site01/foo', LanguageInterface::LANGCODE_SITE_DEFAULT);
    $this->vsiteAliasStorage->delete([
      'alias' => '/site01/foo',
    ]);
    $this->assertNull($this->vsiteAliasStorage->load([
      'alias' => '/site01/foo',
    ]));
  }

}