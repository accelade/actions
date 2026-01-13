import { test, expect } from '@playwright/test';

test.describe('Actions Demo', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/actions-demo');
    });

    test('loads the demo page', async ({ page }) => {
        await expect(page.locator('h1')).toContainText('Actions Demo');
    });

    test('displays button variants', async ({ page }) => {
        const section = page.locator('section').filter({ hasText: 'Button Variants' });

        await expect(section.locator('button, a').first()).toBeVisible();
    });

    test('displays color variants', async ({ page }) => {
        const section = page.locator('section').filter({ hasText: 'Colors' });

        // Should have all color buttons
        await expect(section.locator('button, a')).toHaveCount(12); // 6 solid + 6 outlined
    });

    test('displays size variants', async ({ page }) => {
        const section = page.locator('section').filter({ hasText: 'Sizes' });

        // Should have xs, sm, md, lg, xl buttons
        await expect(section.locator('button, a')).toHaveCount(5);
    });

    test('shows confirmation modal on delete action', async ({ page }) => {
        const deleteButton = page.locator('button, a').filter({ hasText: 'Delete' }).first();

        await deleteButton.click();

        // Should show confirmation dialog
        await expect(page.locator('.accelade-confirm-overlay')).toBeVisible();
        await expect(page.locator('.accelade-confirm-dialog')).toContainText('Delete');
    });

    test('can cancel confirmation modal', async ({ page }) => {
        const deleteButton = page.locator('button, a').filter({ hasText: 'Delete' }).first();

        await deleteButton.click();

        // Click cancel
        await page.locator('.accelade-confirm-btn-cancel').click();

        // Dialog should be closed
        await expect(page.locator('.accelade-confirm-overlay')).not.toBeVisible();
    });

    test('disabled buttons are not clickable', async ({ page }) => {
        const disabledButton = page.locator('button[disabled]').first();

        await expect(disabledButton).toHaveAttribute('disabled');
        await expect(disabledButton).toHaveClass(/opacity-50/);
    });
});
