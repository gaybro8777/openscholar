<?php

namespace Drupal\os_publications\Plugin;

use Drupal\bibcite\CitationStylerInterface;
use Drupal\Core\Plugin\PluginBase;

/**
 * Class SampleCitations.
 *
 * @package Drupal\os_publications\Plugin
 */
class SampleCitations extends PluginBase {

  /**
   * SampleCitations constructor.
   *
   * @param \Drupal\bibcite\CitationStylerInterface $styler
   *   To use styler service.
   */
  public function __construct(CitationStylerInterface $styler) {
    $this->styler = $styler;
  }

  /**
   * Generates Citation examples.
   *
   * @return string
   *   Citation examples.
   */
  public function citeExampleOutput() {
    /** @var string $cite_example_output */
    $cite_example_output = '';
    /** @var string $default_style */
    $default_style = $this->styler->getStyle()->id();

    // Attach extra HTML to the citation array to allow for a popup box that
    // will display an example of the citation format.
    $csl_styles = $this->styler->getAvailableStyles();
    if ($csl_styles) {

      foreach ($csl_styles as $style) {
        // Setup the new citation options to be wrapped for a popup.
        // Setup the h2.
        $cite_example_title = '<h2>' . $this->t('@csl_title', ['@csl_title' => $style->label()]) . '</h2>';
        // Setup the citation exmaple for the popup.
        $citation_example = $this->osPublicationsBuildCitationExample($style->id());
        $cite_example_text = $citation_example;
        // Concat it all together.
        $hidden = ($style->id() != $default_style) ? 'hidden' : '';
        $cite_example_output .= '<div data-example-id="' . $style->id() . '" id="' . str_replace('.', '', $style->id()) . '" class="citebox ' . $hidden . '">' . $cite_example_title . $cite_example_text . '</div>';
      }
      $this->styler->setStyleById($default_style);
    }
    return $cite_example_output;
  }

  /**
   * Generate Citation Example.
   *
   * @param string $csl
   *   The default style.
   *
   * @return string
   *   Renderable citation example.
   */
  public function osPublicationsBuildCitationExample($csl) {
    if (!$csl) {
      // Get the default biblio style.
      $csl = $this->styler->getStyle()->id();
    }
    $node_array = $this->osPublicationsGetCitationExample();
    return $this->osPublicationsThemeCitation(['style_name' => $csl, 'node_array' => $node_array]);
  }

  /**
   * Return a pre-built node for an example citation.
   *
   * @return array
   *   Entity object.
   */
  public function osPublicationsGetCitationExample() {

    // Basic author listing.
    $contributors = [
      [
        'family' => 'Doe',
        'given' => 'John',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Smith',
        'given' => 'Richard',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Editor',
        'given' => 'Edwin',
        'category' => 'primary',
        'role' => 'editor',
      ],
    ];
    $contributors = json_decode(json_encode($contributors), FALSE);

    // 10+ author example to display the different ways "et al" works.
    $contributors_etall = [
      [
        'family' => 'Doe',
        'given' => 'John',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Smith',
        'given' => 'Richard',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Rodgers',
        'given' => 'Edwin',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Howard',
        'given' => 'Ron',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Rodgers',
        'given' => 'Jill',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => "O'Donnell",
        'given' => 'Frank',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'McQuiad',
        'given' => 'Robert',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Smith',
        'given' => 'Jane',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Ortiz',
        'given' => 'Oscar',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Edwards',
        'given' => 'Rebecca',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => "O'Neil",
        'given' => 'Thomas',
        'category' => 'primary',
        'role' => 'author',
      ],
      [
        'family' => 'Smith',
        'given' => 'Kathrine',
        'category' => 'primary',
        'role' => 'author',
      ],
    ];

    $contributors_etall = json_decode(json_encode($contributors_etall), FALSE);
    $date = new \stdClass();
    $date->{'date-parts'} = [['2013']];

    // Book Chapter.
    $node                      = new \stdClass();
    $node->id                  = -1;
    $node->example_type        = 'Book Chapter';
    $node->title               = 'A Book Chapter';
    $node->author              = $contributors;
    $node->type                = 'chapter';
    $node->issued              = $date;
    $node->volume              = 1;
    $node->edition             = 5;
    $node->issue               = 2;
    $node->{'container-title'} = 'My Book Title';
    $node->page                = '500-731';
    $node->bibcite_coins       = '';

    // Journal Article.
    $node1                      = new \stdClass();
    $node1->id                  = -1;
    $node1->example_type        = 'Journal Article';
    $node1->title               = 'My Journal Article';
    $node1->author              = $contributors;
    $node1->type                = 'article-journal';
    $node1->issued              = $date;
    $node1->volume              = 3;
    $node1->issue               = 4;
    $node1->{'container-title'} = 'The Journal of Articles';
    $node1->page                = '25-56';
    $node1->bibcite_coins       = '';

    // Book.
    $node2                      = new \stdClass();
    $node2->id                  = -1;
    $node2->example_type        = 'Book with 10+ authors (et al)';
    $node2->title               = 'This is a Book Title';
    $node2->author              = $contributors_etall;
    $node2->type                = 'book';
    $node2->issued              = $date;
    $node2->volume              = 1;
    $node2->edition             = 5;
    $node2->page                = '800';
    $node2->publisher           = 'Oxford University Press';
    $node2->{'publisher-place'} = 'Boston, MA';
    $node2->bibcite_coins       = '';

    $node_array = [$node, $node1, $node2];
    return $node_array;
  }

  /**
   * Returns styled node.
   *
   * @param array $variables
   *   Node array and default style.
   *
   * @return string
   *   Styled nodes.
   */
  public function osPublicationsThemeCitation(array $variables) {
    /* @var string $styled_node */
    $styled_node = '';
    $node_array = $variables['node_array'];
    $style = $variables['style_name'] ? $variables['style_name'] : NULL;
    $this->styler->setStyleById($style);

    // Display the citation.
    foreach ($node_array as $value) {
      $output = '';
      // Strip off the "example_type" array value if one exists.
      if (isset($value->example_type)) {
        $example_type = '<strong>' . $value->example_type . ' ' . $this->t('example:') . '</strong>';
        $output .= $example_type . '<br />';
      }
      $output .= $this->styler->render($value);
      $styled_node .= '<div class="citation-example">' . $output . '</div>';
    }
    return $styled_node;
  }

}