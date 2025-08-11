#!/usr/bin/env node
/**
 * Build script for PHPDocSpark Documentation Platform
 * Provides detailed logging and error handling for the Vite build process
 */

const { execSync, spawn } = require('child_process');
const fs = require('fs');
const path = require('path');

// Colors for console output
const colors = {
    red: '\x1b[31m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    blue: '\x1b[34m',
    magenta: '\x1b[35m',
    cyan: '\x1b[36m',
    white: '\x1b[37m',
    reset: '\x1b[0m',
    bright: '\x1b[1m'
};

function log(message, color = 'white') {
    const timestamp = new Date().toISOString().substring(11, 19);
    console.log(`${colors.cyan}[${timestamp}]${colors.reset} ${colors[color]}${message}${colors.reset}`);
}

function logError(message, error = null) {
    log(`❌ ERROR: ${message}`, 'red');
    if (error && error.message) {
        console.log(`${colors.red}${error.message}${colors.reset}`);
    }
}

function logSuccess(message) {
    log(`✅ SUCCESS: ${message}`, 'green');
}

function logInfo(message) {
    log(`ℹ️  INFO: ${message}`, 'blue');
}

function logWarning(message) {
    log(`⚠️  WARNING: ${message}`, 'yellow');
}

async function checkFiles() {
    const requiredFiles = [
        'src/main.js',
        'src/css/site.scss',
        'src/css/vendor.scss',
        'src/css/custom.scss',
        'src/js/vendor.js',
        'src/js/custom.js',
        'vite.config.js'
    ];

    logInfo('Checking required files...');
    
    for (const file of requiredFiles) {
        if (!fs.existsSync(file)) {
            logError(`Required file missing: ${file}`);
            process.exit(1);
        }
    }
    
    logSuccess('All required files found');
}

async function cleanAssets() {
    logInfo('Cleaning assets directory...');
    
    const dirs = [
        'website/assets/css',
        'website/assets/js',
        'website/assets/images'
    ];
    
    for (const dir of dirs) {
        try {
            if (fs.existsSync(dir)) {
                fs.rmSync(dir, { recursive: true, force: true });
                logInfo(`Cleaned: ${dir}`);
            }
        } catch (error) {
            logWarning(`Could not clean ${dir}: ${error.message}`);
        }
    }
    
    logSuccess('Assets cleaned');
}

async function runViteBuild() {
    logInfo('Starting Vite build...');
    
    return new Promise((resolve, reject) => {
        const build = spawn('npx', ['vite', 'build'], {
            stdio: 'pipe',
            shell: true
        });
        
        let stdout = '';
        let stderr = '';
        
        build.stdout.on('data', (data) => {
            stdout += data.toString();
            process.stdout.write(data);
        });
        
        build.stderr.on('data', (data) => {
            stderr += data.toString();
            process.stderr.write(data);
        });
        
        build.on('close', (code) => {
            if (code === 0) {
                logSuccess('Vite build completed successfully');
                resolve();
            } else {
                logError(`Vite build failed with exit code ${code}`);
                if (stderr) {
                    console.log(`${colors.red}STDERR:${colors.reset}\n${stderr}`);
                }
                reject(new Error(`Build failed with code ${code}`));
            }
        });
        
        build.on('error', (error) => {
            logError('Failed to start Vite build process', error);
            reject(error);
        });
    });
}

async function copyAssets() {
    logInfo('Copying additional assets...');
    
    // Copy images
    const srcImageDir = 'src/assets/images';
    const destImageDir = 'website/assets/images';
    
    if (fs.existsSync(srcImageDir)) {
        try {
            if (!fs.existsSync(destImageDir)) {
                fs.mkdirSync(destImageDir, { recursive: true });
            }
            
            const files = fs.readdirSync(srcImageDir);
            for (const file of files) {
                const srcPath = path.join(srcImageDir, file);
                const destPath = path.join(destImageDir, file);
                
                if (fs.statSync(srcPath).isFile()) {
                    fs.copyFileSync(srcPath, destPath);
                    logInfo(`Copied image: ${file}`);
                }
            }
            
            logSuccess(`Copied ${files.length} images`);
        } catch (error) {
            logError('Failed to copy images', error);
        }
    } else {
        logWarning('No source images directory found');
    }
    
    // Ensure Bootstrap Icons fonts are in the right place
    const fontDir = 'website/assets/fonts';
    if (!fs.existsSync(fontDir)) {
        fs.mkdirSync(fontDir, { recursive: true });
        logInfo('Created fonts directory');
    }
    
    // Copy Bootstrap Icons fonts if they don't exist
    const fontSources = [
        { src: 'node_modules/bootstrap-icons/font/fonts/bootstrap-icons.woff2', dest: 'website/assets/fonts/bootstrap-icons.woff2' },
        { src: 'node_modules/bootstrap-icons/font/fonts/bootstrap-icons.woff', dest: 'website/assets/fonts/bootstrap-icons.woff' }
    ];
    
    for (const font of fontSources) {
        if (fs.existsSync(font.src) && !fs.existsSync(font.dest)) {
            fs.copyFileSync(font.src, font.dest);
            logInfo(`Copied font: ${path.basename(font.dest)}`);
        }
    }
}

async function verifyOutput() {
    logInfo('Verifying build output...');
    
    const expectedFiles = [
        'website/assets/css/site.css',
        'website/assets/js/site.js'
    ];
    
    for (const file of expectedFiles) {
        if (fs.existsSync(file)) {
            const stats = fs.statSync(file);
            const sizeKB = Math.round(stats.size / 1024);
            logSuccess(`Generated: ${file} (${sizeKB}KB)`);
        } else {
            logError(`Expected output file missing: ${file}`);
        }
    }
}

async function updateVersion() {
    logInfo('Updating version information...');
    
    try {
        execSync('node scripts/update-version.js', { stdio: 'inherit' });
        logSuccess('Version information updated');
    } catch (error) {
        logError('Failed to update version information', error);
        throw error;
    }
}

async function main() {
    const startTime = Date.now();
    
    log('🚀 Starting PHPDocSpark build process...', 'bright');
    
    try {
        await checkFiles();
        await updateVersion();
        await cleanAssets();
        await runViteBuild();
        await copyAssets();
        await verifyOutput();
        
        const duration = ((Date.now() - startTime) / 1000).toFixed(2);
        log(`🎉 Build completed successfully in ${duration}s`, 'green');
        
    } catch (error) {
        const duration = ((Date.now() - startTime) / 1000).toFixed(2);
        logError(`Build failed after ${duration}s`, error);
        process.exit(1);
    }
}

// Handle command line arguments
const args = process.argv.slice(2);
if (args.includes('--help') || args.includes('-h')) {
    console.log(`
PHPDocSpark Build Script

Usage: node scripts/build.js [options]

Options:
  --help, -h    Show this help message
  
This script will:
1. Check for required source files
2. Clean the assets directory
3. Run Vite build process
4. Copy images from src to website
5. Verify output files

Output files:
  - website/assets/css/site.css
  - website/assets/js/site.js
  - website/assets/images/*
    `);
    process.exit(0);
}

main().catch(console.error);
