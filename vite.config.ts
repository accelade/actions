import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    build: {
        lib: {
            entry: resolve(__dirname, 'resources/js/index.ts'),
            name: 'AcceladeActions',
            fileName: 'actions',
            formats: ['iife', 'es'] as const,
        },
        outDir: 'dist',
        emptyOutDir: true,
        rollupOptions: {
            external: [],
            output: [
                {
                    format: 'iife' as const,
                    entryFileNames: 'actions.js',
                    assetFileNames: '[name].[ext]',
                    inlineDynamicImports: true,
                    name: 'AcceladeActionsModule',
                    exports: 'named' as const,
                },
                {
                    format: 'es' as const,
                    entryFileNames: 'actions.esm.js',
                    assetFileNames: '[name].[ext]',
                    inlineDynamicImports: true,
                    exports: 'named' as const,
                },
            ],
        },
        sourcemap: true,
        minify: 'terser' as const,
        terserOptions: {
            compress: {
                drop_console: false,
            },
        },
    },

    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },

    define: {
        'process.env.NODE_ENV': JSON.stringify('production'),
    },
});
