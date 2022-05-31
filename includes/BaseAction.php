<?php

use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\DBConnRef;

abstract class BaseAction extends Action {
	public function show() {
		$user = $this->getUser();
		$out = $this->getOutput();

		$dbw = wfGetDB( DB_PRIMARY );
		$title = $this->getTitle();
		$subject =
			MediaWikiServices::getInstance()
				->getNamespaceInfo()
				->getSubject( $title->getNamespace() );

		if ( $this->doAction( $dbw, $subject, $user, $title ) ) {
			$out->addWikiMsg( $this->successMessage(), $title->getPrefixedText() );
			$user->invalidateCache();
            $this->$success = true;
		} else {
			$out->addWikiMsg( 'favoriteerrortext', $title->getPrefixedText() );
            $this->$success = false;
		}
	}

    public $success;

    /**
     * @return string
     */
    abstract public function getActionType();

	abstract public function successMessage();

	/**
	 * @param DBConnRef $dbw
	 * @param int $subject
	 * @param User $user
	 * @param Title $title
	 * @return bool
	 */
	abstract protected function doAction( DBConnRef $dbw, int $subject, User $user, Title $title );
}
