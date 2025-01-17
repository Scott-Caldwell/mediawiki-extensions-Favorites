<?php

class FavoriteAction extends BaseAction {
	/**
	 * @inheritDoc
	 */
	public function getName() {
		return 'favorite';
	}

    /**
     * @inheritDoc
     */
    public function getActionType() {
        return 'favorited';
    }

	/**
	 * @inheritDoc
	 */
	public function successMessage() {
		return 'addedfavoritetext';
	}

	/**
	 * @param \Wikimedia\Rdbms\DBConnRef $dbw
	 * @param int $subject
	 * @param User $user
	 * @param Title $title
	 * @return bool
	 */
	protected function doAction(
		\Wikimedia\Rdbms\DBConnRef $dbw, int $subject, User $user, Title $title
	) {
		$dbw->insert( 'favoritelist', [
			'fl_user' => $user->getId(),
			'fl_namespace' => $subject,
			'fl_title' => $title->getDBkey(),
			'fl_notificationtimestamp' => null,
		], __METHOD__, 'IGNORE' );

		return $dbw->affectedRows() === 1;
	}
}
