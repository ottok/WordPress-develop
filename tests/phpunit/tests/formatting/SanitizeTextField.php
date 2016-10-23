<?php

/**
 * @group formatting
 */
class Tests_Formatting_SanitizeTextField extends WP_UnitTestCase {
	// #11528
	function test_sanitize_text_field() {
		$inputs = array(
			'оРангутанг', //Ensure UTF8 text is safe the Р is D0 A0 and A0 is the non-breaking space.
			'САПР', //Ensure UTF8 text is safe the Р is D0 A0 and A0 is the non-breaking space.
			'one is < two',
			'tags <span>are</span> <em>not allowed</em> here',
			' we should trim leading and trailing whitespace ',
			'we  trim  extra  internal  whitespace  only  in  single  line  texts',
			"tabs \tget removed in single line texts",
			"newlines are allowed only\n in multiline texts",
			'We also %AB remove %ab octets',
			'We don\'t need to wory about %A
			B removing %a
			b octets even when %a	B they are obscured by whitespace',
			'%AB%BC%DE', //Just octets
			'Invalid octects remain %II',
			'Nested octects %%%ABABAB %A%A%ABBB',
		);
		$expected = array(
			'оРангутанг',
			'САПР',
			'one is &lt; two',
			'tags are not allowed here',
			'we should trim leading and trailing whitespace',
			array(
				'oneline' => 'we trim extra internal whitespace only in single line texts',
				'multiline' => 'we  trim  extra  internal  whitespace  only  in  single  line  texts'
			),
			array(
				'oneline' => 'tabs get removed in single line texts',
				'multiline' => "tabs \tget removed in single line texts"
			),
			array(
				'oneline' => 'newlines are allowed only in multiline texts',
				'multiline' => "newlines are allowed only\n in multiline texts"
			),
			'We also remove octets',
			array (
				'oneline' => 'We don\'t need to wory about %A B removing %a b octets even when %a B they are obscured by whitespace',
				'multiline' => "We don't need to wory about %A\n			B removing %a\n			b octets even when %a	B they are obscured by whitespace"
			),
			'', //Emtpy as we strip all the octets out
			'Invalid octects remain %II',
			'Nested octects',
		);

		foreach ($inputs as $key => $input) {
			$this->assertEquals( is_array( $expected[$key] ) ? $expected[$key]['oneline'] : $expected[$key] , sanitize_text_field($input));
			$this->assertEquals( is_array( $expected[$key] ) ? $expected[$key]['multiline'] : $expected[$key] , sanitize_textarea_field($input));
		}
	}
}
