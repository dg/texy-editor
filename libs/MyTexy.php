<?php



class MyTexy extends Texy
{

	function __construct()
	{
		parent::__construct();

		$this->linkModule->root         = '';
		$this->imageModule->root        = 'images/';
		$this->imageModule->fileRoot  = 'images/';
		$this->imageModule->linkedRoot  = 'images/';
		$this->alignClasses['left'] = 'left';
		$this->alignClasses['right'] = 'right';
		$this->emoticonModule->root    = 'images/';
		$this->emoticonModule->class   = 'smiley';
		$this->headingModule->top = 1;
		$this->headingModule->generateID = TRUE;

		$this->addHandler('block', array($this, 'blockHandler'));
		$this->addHandler('script', array($this, 'scriptHandler'));
		$this->addHandler('phrase', array($this, 'phraseHandler'));

		$link = new TexyLink('http://www.google.com/search?q=%s');
		$this->linkModule->addReference('google', $link);

		$link = new TexyLink('http://en.wikipedia.org/wiki/Special:Search?search=%s');
		$this->linkModule->addReference('wikipedia', $link);

		$link = new TexyLink('http://php.net/%s');
		$this->linkModule->addReference('php', $link);
	}



	/**
	 * @param TexyHandlerInvocation  handler invocation
	 * @param string  command
	 * @param array   arguments
	 * @param string  arguments in raw format
	 * @return TexyHtml|string|FALSE
	 */
	public function scriptHandler($invocation, $cmd, $args, $raw)
	{
		return '';
	}



	/**
	 * @param TexyHandlerInvocation  handler invocation
	 * @param string
	 * @param string
	 * @param TexyModifier
	 * @param TexyLink
	 * @return TexyHtml|string|FALSE
	 */
	public function phraseHandler($invocation, $phrase, $content, $modifier, $link)
	{
		if (!$link) {
			$el = $invocation->proceed();
			if ($el instanceof TexyHtml && $el->getName() !== 'a' && $el->title !== NULL) {
				$el->class[] = 'about';
			}
			return $el;
		}

		return $invocation->proceed();
	}



	/**
	 * User handler for code block
	 *
	 * @param TexyHandlerInvocation  handler invocation
	 * @param string  block type
	 * @param string  text to highlight
	 * @param string  language
	 * @param TexyModifier modifier
	 * @return TexyHtml
	 */
	function blockHandler($invocation, $blocktype, $content, $lang, $modifier)
	{
		if ($blocktype !== 'block/code') {
			return $invocation->proceed();
		}

		$lang = strtoupper($lang);
		if ($lang == 'JAVASCRIPT') $lang = 'JS';

		$parser = new fshlParser('HTML_UTF8', P_TAB_INDENT);
		if (!$parser->isLanguage($lang)) {
			return $invocation->proceed();
		}

		$content = Texy::outdent($content);
		$content = $parser->highlightString($lang, $content);
		$content = $this->protect($content, Texy::CONTENT_BLOCK);

		$elPre = TexyHtml::el('pre');
		if ($modifier) $modifier->decorate($this, $elPre);
		$elPre->attrs['class'] = strtolower($lang);

		$elCode = $elPre->create('code', $content);

		return $elPre;
	}

}
