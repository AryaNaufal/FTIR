import { defineConfig } from '@playwright/test';
export default defineConfig({testDir:'tests/browser',timeout:60000,workers:1,use:{baseURL:process.env.FTIR_TEST_URL||'http://127.0.0.1:8000',channel:'chrome',headless:true,viewport:{width:1440,height:1000}},reporter:'list'});
