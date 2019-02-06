<?php
/**
 * Tests to ensure creole syntax is correctly processed
 * and creates expected XHTML output
 *
 * @group plugin_creole
 * @group plugins
 */
class plugin_creole_test extends DokuWikiTest {
    protected $pluginsEnabled = array('creole');

    function setUp(){
        global $conf;

        parent::setUp();

        $conf ['plugin']['creole']['precedence'] = 'Creole';
        $conf ['plugin']['creole']['linebreak']  = 'Whitespace';
    }

    /**
     * Test monospace XHTML generation.
     * Recommended output: "This is <tt>monospace</tt> text."
     * See http://www.wikicreole.org/wiki/CreoleAdditions, section "Monospace"
     */
    public function test_monospace() {
        $info = array();
        $expected = "\n<p>\nThis is <tt>monospace</tt> text. \n</p>\n";

        $instructions = p_get_instructions('This is ##monospace## text.');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test superscript XHTML generation.
     * Recommended output: "This is <sup>superscripted</sup> text."
     * See http://www.wikicreole.org/wiki/CreoleAdditions, section "Superscript"
     */
    public function test_superscript() {
        $info = array();
        $expected = "\n<p>\nThis is <sup>superscripted</sup> text. \n</p>\n";

        $instructions = p_get_instructions('This is ^^superscripted^^ text.');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test subscript XHTML generation.
     * Recommended output: "This is <sub>subscripted</sub> text."
     * See http://www.wikicreole.org/wiki/CreoleAdditions, section "Superscript"
     */
    public function test_subscript() {
        $info = array();
        $expected = "\n<p>\nThis is <sub>subscripted</sub> text. \n</p>\n";

        $instructions = p_get_instructions('This is ,,subscripted,, text.');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 1, no trailing equal signs
     */
    public function test_heading1_no_trailing_equal() {
        $info = array();
        $expected = "\n<h1 class=\"sectionedit1\" id=\"top-level_heading_1\">Top-level heading (1)</h1>\n<div class=\"level1\">\n\n</div>\n";

        $instructions = p_get_instructions('= Top-level heading (1)');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 2, no trailing equal signs
     */
    public function test_heading2_no_trailing_equal() {
        $info = array();
        $expected = "\n<h2 class=\"sectionedit1\" id=\"this_a_test_for_creole_01_2\">This a test for creole 0.1 (2)</h2>\n<div class=\"level2\">\n\n</div>\n";

        $instructions = p_get_instructions('== This a test for creole 0.1 (2)');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 3, no trailing equal signs
     */
    public function test_heading3_no_trailing_equal() {
        $info = array();
        $expected = "\n<h3 class=\"sectionedit1\" id=\"this_is_a_subheading_3\">This is a Subheading (3)</h3>\n<div class=\"level3\">\n\n</div>\n";

        $instructions = p_get_instructions('=== This is a Subheading (3)');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 4, no trailing equal signs
     */
    public function test_heading4_no_trailing_equal() {
        $info = array();
        $expected = "\n<h4 id=\"subsub_4\">Subsub (4)</h4>\n<div class=\"level4\">\n\n</div>\n";

        $instructions = p_get_instructions('==== Subsub (4)');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 5, no trailing equal signs
     */
    public function test_heading5_no_trailing_equal() {
        $info = array();
        $expected = "\n<h5 id=\"subsubsub_5\">Subsubsub (5)</h5>\n<div class=\"level5\">\n\n</div>\n";

        $instructions = p_get_instructions('===== Subsubsub (5)');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 1
     */
    public function test_heading1() {
        $info = array();
        $expected = "\n<h1 class=\"sectionedit1\" id=\"top-level_heading_1\">Top-level heading (1)</h1>\n<div class=\"level1\">\n\n</div>\n";

        $instructions = p_get_instructions('= Top-level heading (1) =');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 2
     */
    public function test_heading2() {
        $info = array();
        $expected = "\n<h2 class=\"sectionedit1\" id=\"this_a_test_for_creole_01_2\">This a test for creole 0.1 (2)</h2>\n<div class=\"level2\">\n\n</div>\n";

        $instructions = p_get_instructions('== This a test for creole 0.1 (2) ==');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 3
     */
    public function test_heading3() {
        $info = array();
        $expected = "\n<h3 class=\"sectionedit1\" id=\"this_is_a_subheading_3\">This is a Subheading (3)</h3>\n<div class=\"level3\">\n\n</div>\n";

        $instructions = p_get_instructions('=== This is a Subheading (3) ===');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 4
     */
    public function test_heading4() {
        $info = array();
        $expected = "\n<h4 id=\"subsub_4\">Subsub (4)</h4>\n<div class=\"level4\">\n\n</div>\n";

        $instructions = p_get_instructions('==== Subsub (4) ====');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test heading 5
     */
    public function test_heading5() {
        $info = array();
        $expected = "\n<h5 id=\"subsubsub_5\">Subsubsub (5)</h5>\n<div class=\"level5\">\n\n</div>\n";

        $instructions = p_get_instructions('===== Subsubsub (5) =====');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test inline bold and italic style and both styles combined
     */
    public function test_bold_and_italic_inline() {
        $info = array();
        $expected = "\n<p>\nYou can make things <strong>bold</strong> or <em>italic</em> or <strong><em>both</em></strong> or <em><strong>both</strong></em>. \n</p>\n";

        $instructions = p_get_instructions('You can make things **bold** or //italic// or **//both//** or //**both**//.');
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }

    /**
     * Test according to examples in:
     * http://www.wikicreole.org/attach/Creole1.0TestCases/creole1.0test.txt
     *
     * Test bold multiline
     */
    public function test_bold_multiline() {
        $info = array();
        $expected = "\n<p>\nCharacter formatting extends across line breaks: <strong>bold, this is still bold. This line deliberately does not end in star-star.</strong>\n</p>\n\n<p>\nNot bold. Character formatting does not cross paragraph boundaries. \n</p>\n";

        $source = "Character formatting extends across line breaks: **bold,\nthis is still bold. This line deliberately does not end in star-star.\n\nNot bold. Character formatting does not cross paragraph boundaries.";
        $instructions = p_get_instructions($source);
        $xhtml = p_render('xhtml', $instructions, $info);

        $this->assertEquals($expected, $xhtml);
    }
}
