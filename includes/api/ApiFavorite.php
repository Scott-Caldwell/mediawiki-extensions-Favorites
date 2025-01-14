<?php

/**
 * API module to allow users to favorite a page
 *
 * @ingroup API
 */
class ApiFavorite extends ApiBase {

	/**
	 * @param ApiMain $main
	 * @param string $action
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		$user = $this->getUser();
		if ( !$user->isRegistered() ) {
			$this->dieWithError( 'You must be logged-in to have a favoritelist', 'notloggedin' );
		}

		$params = $this->extractRequestParams();
		$title = Title::newFromText( $params['title'] );

		if ( !$title || $title->getNamespace() < 0 ) {
			$this->dieWithError( [ 'invalidtitle', $params['title'] ] );
		}

		$res = [ 'title' => $title->getPrefixedText() ];
        $article = Article::newFromTitle( $title, $this );

		if ( $params['unfavorite'] ) {
            $action = new UnfavoriteAction( $article, $this );
		} else {
            $action = new FavoriteAction( $article, $this );
        }

        $action->show();

        if ( !$action->$success ) {
			$this->dieWithError( 'hookaborted' );
        }

        $res[$action->getActionType()] = '';
        $res['message'] = $this->msg( $action->successMessage(), $title->getPrefixedText() )->title( $title )->parseAsBlock();

		$this->getResult()->addValue( null, $this->getModuleName(), $res );
	}

	/**
	 * @return bool
	 */
	public function mustBePosted() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function isWriteMode() {
		return true;
	}

	// since this makes changes the database, we should use this, but I just can't get it to work.
	//public function needsToken() {
	//	return 'favorite';
	//}

	//public function getTokenSalt() {
	//	return 'favorite';
	//}

	/**
	 * @inheritDoc
	 */
	public function getAllowedParams() {
		return [
			'title' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			],
			'unfavorite' => false,
			// 'token' => array(
			//	ApiBase::PARAM_TYPE => 'string',
			//	ApiBase::PARAM_REQUIRED => true
			//),
		];
	}

	/**
	 * @return string[]
	 */
	public function getParamDescription() {
		return [
			'title' => 'The page to (un)favorite',
			'unfavorite' => 'If set the page will be unfavorited rather than favorited',
			'token' => 'A token previously acquired via prop=info',
		];
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return 'Add or remove a page from/to the current user\'s favoritelist';
	}

	/**
	 * @return string[]
	 */
	public function getExamples() {
		return [
			'api.php?action=favorite&title=Main_Page' => 'Favorite the page "Main Page"',
			'api.php?action=favorite&title=Main_Page&unfavorite=' => 'Unfavorite the page "Main Page"',
		];
	}

	/**
	 * @return string
	 */
	public function getHelpUrls() {
		return 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Favorites';
	}
}
