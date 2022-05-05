/**
 * External dependencies
 */
import type { Browser, Page, BrowserContext } from '@playwright/test';

/**
 * Internal dependencies
 */
/**
 * Internal dependencies
 */
import { createNewPost } from './create-new-post';
import { getPageError } from './get-page-error';
import { visitAdminPage } from './visit-admin-page';
import { visitSiteEditor } from './visit-site-editor';
import type { PageUtils } from '../page-utils';

export class Admin {
	browser: Browser;
	page: Page;
	pageUtils: PageUtils;
	context: BrowserContext;

	constructor( page: Page, pageUtils: PageUtils ) {
		this.page = page;
		this.context = page.context();
		this.browser = this.context.browser()!;
		this.pageUtils = pageUtils;
	}

	createNewPost = createNewPost;
	getPageError = getPageError;
	visitAdminPage = visitAdminPage;
	visitSiteEditor = visitSiteEditor;
}
