import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig(({ mode }) => {
  const isDev = mode === 'development';
  
  return {
    // Entry points
    build: {
      rollupOptions: {
        input: {
          main: resolve(__dirname, 'src/main.js'),
        },
        output: {
          entryFileNames: 'js/site.js',
          chunkFileNames: 'js/[name].js',
          assetFileNames: (assetInfo) => {
            const info = assetInfo.name.split('.');
            const extType = info[info.length - 1];
            if (/\.(css)$/.test(assetInfo.name)) {
              return 'css/site.css';
            }
            if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico)$/i.test(assetInfo.name)) {
              return 'images/[name][extname]';
            }
            if (/\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
              return 'fonts/[name][extname]';
            }
            return `assets/[name][extname]`;
          },
        },
      },
      outDir: 'website/assets',
      emptyOutDir: false, // Don't empty the entire assets dir, just our built files
      minify: isDev ? false : 'terser',
      terserOptions: {
        compress: {
          drop_console: !isDev,
          drop_debugger: true,
        },
        format: {
          comments: false,
        },
      },
      sourcemap: isDev,
      reportCompressedSize: !isDev,
      chunkSizeWarningLimit: 1000,
    },
    
    // CSS preprocessing
    css: {
      preprocessorOptions: {
        scss: {
          includePaths: ['node_modules'],
          silenceDeprecations: ['legacy-js-api'], // Suppress Sass deprecation warnings
        },
      },
      devSourcemap: isDev,
    },

    // Asset handling
    assetsInclude: ['**/*.png', '**/*.jpg', '**/*.jpeg', '**/*.gif', '**/*.svg', '**/*.webp'],

    // Development server (not needed for PHP but useful for asset serving)
    server: {
      port: 3000,
      open: false,
      cors: true,
      hmr: {
        port: 3001,
      },
    },

    // Build options
    define: {
      'process.env.NODE_ENV': JSON.stringify(mode || 'production'),
    },

    // Optimization
    optimizeDeps: {
      include: ['bootstrap', 'jquery', 'datatables.net', 'datatables.net-bs5', 'prismjs'],
    },

    // Plugin configuration
    plugins: [],
    
    // Modern JS features
    esbuild: {
      target: 'es2020',
      drop: isDev ? [] : ['console', 'debugger'],
    },
  };
});
