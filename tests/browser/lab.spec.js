import { test, expect } from '@playwright/test';

test('login, dashboard dokumen, modal input, dan navigasi mobile', async ({ page }) => {
    test.skip(!process.env.FTIR_TEST_PASSWORD, 'FTIR_TEST_PASSWORD diperlukan untuk database demo lokal.');
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));

    await page.goto('/login');
    await page.locator('[name=email]').fill('admin');
    await page.locator('[name=password]').fill(process.env.FTIR_TEST_PASSWORD);
    await page.getByRole('button', { name: 'Masuk ke ruang kerja' }).click();
    await expect(page.getByRole('heading', { name: 'Dashboard FTIR', exact: true })).toBeVisible();
    await expect(page.locator('#document-monitoring-chart')).toBeVisible();
    await page.screenshot({ path: 'storage/app/dashboard-desktop.png', fullPage: true });

    await page.goto('/samples');
    await page.getByRole('button', { name: 'Input dokumen Part', exact: true }).click();
    await expect(page.getByRole('heading', { name: 'Input dokumen Part', exact: true })).toBeVisible();
    await expect(page.locator('[name=document_part]')).toBeVisible();
    await page.getByRole('button', { name: 'Tutup form input' }).click();

    await page.goto('/validations');
    await expect(page.getByRole('heading', { name: 'Validasi FTIR', exact: true })).toBeVisible();
    await page.getByRole('button', { name: /validasi/i }).first().click();
    await expect(page.getByRole('heading', { name: /Part [AB]/ })).toBeVisible();
    await page.getByRole('button', { name: 'Tutup', exact: true }).click();

    await page.goto('/materials');
    await expect(page.getByRole('heading', { name: 'Master bahan baku', exact: true })).toBeVisible();
    await page.getByRole('button', { name: 'Tambah bahan baku', exact: true }).click();
    await expect(page.getByRole('heading', { name: 'Tambah bahan baku', exact: true })).toBeVisible();
    await page.getByRole('button', { name: 'Tutup', exact: true }).click();

    await page.goto('/users');
    await expect(page.getByRole('heading', { name: 'Manajemen pengguna', exact: true })).toBeVisible();
    await page.getByRole('button', { name: 'Detail', exact: true }).first().click();
    await expect(page.getByRole('heading', { name: 'Detail pengguna', exact: true })).toBeVisible();
    await page.getByRole('button', { name: 'Tutup detail pengguna' }).click();

    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/');
    await page.getByRole('button', { name: 'Buka navigasi' }).click();
    await expect(page.locator('.sidebar')).toHaveClass(/open/);
    await page.getByRole('link', { name: 'Pemantauan FTIR', exact: false }).click();
    await expect(page.getByRole('heading', { name: 'Pemantauan FTIR', exact: true })).toBeVisible();
    await page.screenshot({ path: 'storage/app/samples-mobile.png', fullPage: true });
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth)).toBeTruthy();
    expect(errors).toEqual([]);
});
