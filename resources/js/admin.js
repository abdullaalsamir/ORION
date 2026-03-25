import './bootstrap';

import * as Turbo from "@hotwired/turbo";

import ace from 'ace-builds/src-noconflict/ace';
import 'ace-builds/src-noconflict/mode-html';
import 'ace-builds/src-noconflict/theme-github';
import 'ace-builds/src-noconflict/ext-searchbox';
import aceWorkerUrl from 'ace-builds/src-noconflict/worker-html?url';
ace.config.setModuleUrl('ace/mode/html_worker', aceWorkerUrl);

import tinymce from 'tinymce';
import 'tinymce/icons/default/icons.min.js';
import 'tinymce/themes/silver/theme.min.js';
import 'tinymce/models/dom/model.min.js';
import 'tinymce/skins/ui/oxide/skin.js';
import 'tinymce/skins/ui/oxide/content.js';
import 'tinymce/skins/content/default/content.js';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/code';
window.tinymce = tinymce;

import * as pdfjsLib from 'pdfjs-dist';
import pdfWorker from 'pdfjs-dist/build/pdf.worker?url';
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorker;
window.pdfjsLib = pdfjsLib;

Turbo.start();

import { initGlobalHelpers } from './admin/core/helpers';
import { initLayoutUI, updateSidebarActiveState, initTreeLogic } from './admin/core/layout';
import { initMenuPage } from './admin/pages/menus';
import { initPagesPage } from './admin/pages/pages';
import { initBannersPage } from './admin/pages/banners';
import { initSlidersPage } from './admin/pages/sliders';
import { initDirectorsPage } from './admin/pages/directors';
import { initLeadershipPage } from './admin/pages/leadership';
import { initConcernsPage } from './admin/pages/concerns';
import { initVideoGalleryPage } from './admin/pages/videoGallery';
import { initNewsPage } from './admin/pages/news';
import { initCSRPage } from './admin/pages/csr';
import { initCareerPage } from './admin/pages/career';
import { initConnectsPage } from './admin/pages/connects';
import { initFooterPage } from './admin/pages/footer';
import { initSettingsPage } from './admin/pages/settings';

document.addEventListener('turbo:before-visit', (event) => {
    updateSidebarActiveState(event.detail.url);
});

let sidebarScrollTop = 0;

document.addEventListener('turbo:before-render', () => {
    const nav = document.querySelector('.sidebar-nav');
    if (nav) sidebarScrollTop = nav.scrollTop;
});

document.addEventListener('turbo:render', () => {
    const nav = document.querySelector('.sidebar-nav');
    if (nav) nav.scrollTop = sidebarScrollTop;
});

document.addEventListener('turbo:before-cache', (event) => {
    event.preventDefault();
});

document.addEventListener('turbo:load', () => {
    initGlobalHelpers();
    initLayoutUI();
    updateSidebarActiveState();
    initTreeLogic();
    initMenuPage();
    initPagesPage();
    initBannersPage();
    initSlidersPage();
    initDirectorsPage();
    initLeadershipPage();
    initConcernsPage();
    initVideoGalleryPage();
    initNewsPage();
    initCSRPage();
    initCareerPage();
    initConnectsPage();
    initFooterPage();
    initSettingsPage();
});