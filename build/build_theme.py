#!/usr/bin/env python3
"""
TugasinWP Theme Build Script
Generates minified CSS bundle, critical CSS, JS bundle, and creates a distributable zip file.

Usage:
    python build_theme.py
    python build_theme.py --version 2.9.1
    python build_theme.py --no-minify

@package TugasinWP
@since 2.9.0
"""

import os
import sys
import re
import zipfile
import argparse
from pathlib import Path
from datetime import datetime

# Configuration
THEME_NAME = "tugasinwp"
OUTPUT_FOLDER = "." # Output to current directory (tugasinwp/build)

# CSS folders to scan (in order of priority)
CSS_FOLDERS = [
    "assets/css/base",
    "assets/css/components",
    "assets/css/layout",
    "assets/css/pages",
    "assets/css/utilities",
]

# Files to include after folders (in order)
CSS_ROOT_FILES = [
    "assets/css/_responsive.css",
]

# Critical CSS files (above-the-fold content) - order matters
CRITICAL_CSS_FILES = [
    "assets/css/base/_variables.css",
    "assets/css/base/_reset.css",
    "assets/css/base/_typography.css",
    "assets/css/layout/_header.css",
    "assets/css/layout/_hero.css",
]

# JavaScript files to bundle
JS_FILES = [
    "assets/js/main.js",
]

# Files to exclude from bundle
CSS_EXCLUDE = [
    "main-bundle.css",
    "main-bundle.min.css",
    "critical.css",
]

# Files/folders to exclude from zip
EXCLUDE_PATTERNS = [
    ".git",
    ".github",
    ".gitignore",
    ".secret",
    ".secrets",
    ".env",
    ".env.local",
    ".DS_Store",
    "Thumbs.db",
    "node_modules",
    "*.log",
    "*.map",
    ".vscode",
    ".idea",
    "__pycache__",
    "*.bak",
    "*.tmp",
    "build", # Exclude the build directory itself
    "dist",
]


def get_script_dir():
    """Get the directory where this script is located."""
    return Path(__file__).parent.resolve()


def minify_css(css_content: str) -> str:
    """
    Minify CSS content by removing comments, whitespace, and newlines.
    
    Args:
        css_content: The CSS string to minify
        
    Returns:
        Minified CSS string
    """
    # Remove CSS comments (/* ... */)
    css = re.sub(r'/\*[\s\S]*?\*/', '', css_content)
    
    # Remove newlines and carriage returns
    css = re.sub(r'[\r\n]+', '', css)
    
    # Remove extra whitespace
    css = re.sub(r'\s+', ' ', css)
    
    # Remove spaces around specific characters
    css = re.sub(r'\s*([{};:,>+~])\s*', r'\1', css)
    
    # Remove spaces around parentheses
    css = re.sub(r'\s*\(\s*', '(', css)
    css = re.sub(r'\s*\)\s*', ')', css)
    
    # Remove trailing semicolons before closing braces
    css = re.sub(r';}', '}', css)
    
    # Remove leading/trailing whitespace
    css = css.strip()
    
    return css


def minify_js(js_content: str) -> str:
    """
    Minify JavaScript content by removing comments, whitespace, and newlines.
    This is a basic minifier - for production, consider using a proper tool like terser.
    
    Args:
        js_content: The JavaScript string to minify
        
    Returns:
        Minified JavaScript string
    """
    # Remove single-line comments (but not URLs like http://)
    js = re.sub(r'(?<!:)//[^\n]*', '', js_content)
    
    # Remove multi-line comments
    js = re.sub(r'/\*[\s\S]*?\*/', '', js)
    
    # Remove newlines and carriage returns
    js = re.sub(r'[\r\n]+', '\n', js)
    
    # Remove leading/trailing whitespace from each line
    lines = [line.strip() for line in js.split('\n') if line.strip()]
    
    # Join lines with appropriate separators
    result = []
    for i, line in enumerate(lines):
        result.append(line)
        # Add semicolon if line doesn't end with control character
        if i < len(lines) - 1:
            last_char = line[-1] if line else ''
            next_first = lines[i + 1][0] if lines[i + 1] else ''
            
            # Don't add anything if line ends with these
            if last_char in '{([,;:':
                pass
            elif next_first in '}])':
                pass
            else:
                # Add newline to prevent statement merging issues
                result.append('\n')
    
    js = ''.join(result)
    
    # Remove extra spaces (but be careful around operators)
    js = re.sub(r'  +', ' ', js)
    
    # Remove spaces around certain operators
    js = re.sub(r'\s*([{};,:\[\]])\s*', r'\1', js)
    
    # Clean up extra newlines
    js = re.sub(r'\n+', '\n', js)
    
    return js.strip()


def discover_css_files(theme_path: Path) -> list:
    """
    Discover all CSS files from configured folders.
    
    Args:
        theme_path: Path to the theme folder
        
    Returns:
        List of CSS file paths (relative to theme)
    """
    css_files = []
    
    # Scan each folder
    for folder in CSS_FOLDERS:
        folder_path = theme_path / folder
        if folder_path.exists():
            # Get all .css files in the folder, sorted alphabetically
            files = sorted(folder_path.glob("*.css"))
            for f in files:
                rel_path = str(f.relative_to(theme_path)).replace("\\", "/")
                if f.name not in CSS_EXCLUDE:
                    css_files.append(rel_path)
    
    # Add root files at the end
    for css_file in CSS_ROOT_FILES:
        css_path = theme_path / css_file
        if css_path.exists():
            css_files.append(css_file)
    
    return css_files


def generate_css_bundle(theme_path: Path, minify: bool = True) -> bool:
    """
    Concatenate all CSS files into main-bundle.css (minified).
    
    Args:
        theme_path: Path to the theme folder
        minify: Whether to minify the output
        
    Returns:
        True if successful, False otherwise
    """
    print("\n📦 Generating CSS Bundle...")
    
    # Discover CSS files
    css_files = discover_css_files(theme_path)
    
    print(f"   Found {len(css_files)} CSS files\n")
    
    bundle_content = []
    header = f"/**\n * TugasinWP Main Bundle CSS\n * Auto-generated on {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n * DO NOT EDIT - This file is generated from source CSS files\n */\n"
    
    if not minify:
        bundle_content.append(header)
    
    missing_files = []
    
    for css_file in css_files:
        css_path = theme_path / css_file
        if css_path.exists():
            print(f"  ✓ {css_file}")
            with open(css_path, 'r', encoding='utf-8') as f:
                content = f.read()
                if not minify:
                    bundle_content.append(f"\n/* === {css_file} === */\n")
                bundle_content.append(content)
        else:
            print(f"  ✗ {css_file} (not found)")
            missing_files.append(css_file)
    
    if missing_files:
        print(f"\n⚠️  Warning: {len(missing_files)} CSS file(s) not found")
    
    # Combine all content
    combined = '\n'.join(bundle_content)
    
    # Minify if requested
    if minify:
        print("\n🔧 Minifying CSS...")
        original_size = len(combined)
        combined = minify_css(combined)
        minified_size = len(combined)
        savings = ((original_size - minified_size) / original_size) * 100
        print(f"   Reduced: {original_size / 1024:.1f} KB → {minified_size / 1024:.1f} KB ({savings:.1f}% smaller)")
    
    # Write bundle
    bundle_path = theme_path / "assets/css/main-bundle.css"
    with open(bundle_path, 'w', encoding='utf-8') as f:
        f.write(combined)
    
    print(f"\n✅ Bundle created: {bundle_path}")
    print(f"   Size: {bundle_path.stat().st_size / 1024:.1f} KB")
    
    return True


def generate_critical_css(theme_path: Path, minify: bool = True) -> bool:
    """
    Generate critical CSS from header/hero styles.
    
    Args:
        theme_path: Path to the theme folder
        minify: Whether to minify the output
        
    Returns:
        True if successful, False otherwise
    """
    print("\n⚡ Generating Critical CSS...")
    
    critical_content = []
    header = f"""/**
 * TugasinWP Critical CSS
 * Auto-generated on {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
 * Inlined in <head> for above-the-fold content
 * Contains: Variables, Reset, Typography, Header, Hero
 * DO NOT EDIT - This file is generated from source CSS files
 * @package TugasinWP
 */

"""
    
    if not minify:
        critical_content.append(header)
    
    for css_file in CRITICAL_CSS_FILES:
        css_path = theme_path / css_file
        if css_path.exists():
            print(f"  ✓ {css_file}")
            with open(css_path, 'r', encoding='utf-8') as f:
                content = f.read()
                if not minify:
                    critical_content.append(f"\n/* === {css_file} === */\n")
                critical_content.append(content)
        else:
            print(f"  ✗ {css_file} (not found)")
    
    # Combine all content
    combined = '\n'.join(critical_content)
    
    # Minify if requested
    if minify:
        print("\n🔧 Minifying Critical CSS...")
        original_size = len(combined)
        combined = minify_css(combined)
        minified_size = len(combined)
        savings = ((original_size - minified_size) / original_size) * 100
        print(f"   Reduced: {original_size / 1024:.1f} KB → {minified_size / 1024:.1f} KB ({savings:.1f}% smaller)")
    
    # Write critical CSS
    critical_path = theme_path / "assets/css/critical.css"
    with open(critical_path, 'w', encoding='utf-8') as f:
        f.write(combined)
    
    print(f"\n✅ Critical CSS created: {critical_path}")
    print(f"   Size: {critical_path.stat().st_size / 1024:.1f} KB")
    
    return True


def generate_js_bundle(theme_path: Path, minify: bool = True) -> bool:
    """
    Concatenate and minify JavaScript files.
    
    Args:
        theme_path: Path to the theme folder
        minify: Whether to minify the output
        
    Returns:
        True if successful, False otherwise
    """
    print("\n📜 Processing JavaScript...")
    
    for js_file in JS_FILES:
        js_path = theme_path / js_file
        if js_path.exists():
            print(f"  ✓ {js_file}")
            
            with open(js_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_size = len(content)
            
            if minify:
                # Create minified version
                min_path = js_path.with_suffix('.min.js')
                minified = minify_js(content)
                minified_size = len(minified)
                savings = ((original_size - minified_size) / original_size) * 100
                
                with open(min_path, 'w', encoding='utf-8') as f:
                    f.write(minified)
                
                print(f"     Minified: {original_size / 1024:.1f} KB → {minified_size / 1024:.1f} KB ({savings:.1f}% smaller)")
                print(f"     Created: {min_path.name}")
            
        else:
            print(f"  ✗ {js_file} (not found)")
    
    print("\n✅ JavaScript processing complete")
    
    return True


def should_exclude(path: str) -> bool:
    """Check if a path should be excluded from the zip."""
    path_parts = Path(path).parts
    
    for pattern in EXCLUDE_PATTERNS:
        if pattern.startswith("*"):
            # Wildcard pattern (e.g., *.log)
            ext = pattern[1:]
            if path.endswith(ext):
                return True
        else:
            # Exact match
            if pattern in path_parts:
                return True
    
    return False


def create_zip(theme_path: Path, output_path: Path, version: str) -> Path:
    """
    Create a zip file of the theme.
    
    Args:
        theme_path: Path to the theme folder
        output_path: Path to output directory
        version: Version string for the zip filename
        
    Returns:
        Path to the created zip file
    """
    print("\n📁 Creating ZIP archive...")
    
    # Create output directory if needed
    output_path.mkdir(parents=True, exist_ok=True)
    
    # Generate zip filename
    timestamp = datetime.now().strftime('%Y%m%d')
    zip_filename = f"{THEME_NAME}-{version}-{timestamp}.zip"
    zip_path = output_path / zip_filename
    
    file_count = 0
    
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(theme_path):
            # Sort directories to ensure deterministic order
            dirs.sort()
            
            # Filter out excluded directories in-place
            # Iterate through a copy of the list so we can modify the original
            for d in list(dirs):
                if should_exclude(d) or should_exclude(os.path.join(root, d)):
                    dirs.remove(d)
            
            # Sort files to ensure deterministic order
            files.sort()
            
            for file in files:
                file_path = os.path.join(root, file)
                
                if should_exclude(file_path):
                    continue
                
                # Calculate relative path within the zip
                # We want the zip to have a root folder named THEME_NAME
                rel_path = os.path.relpath(file_path, theme_path)
                arcname = os.path.join(THEME_NAME, rel_path)
                
                zipf.write(file_path, arcname)
                file_count += 1
    
    print(f"\n✅ ZIP created: {zip_path}")
    print(f"   Files: {file_count}")
    print(f"   Size: {zip_path.stat().st_size / 1024 / 1024:.2f} MB")
    
    return zip_path


def get_version_from_style(theme_path: Path) -> str:
    """Extract version from style.css."""
    style_path = theme_path / "style.css"
    
    if style_path.exists():
        with open(style_path, 'r', encoding='utf-8') as f:
            for line in f:
                if line.strip().startswith("Version:"):
                    return line.split(":", 1)[1].strip()
    
    return "1.0.0"


def main():
    parser = argparse.ArgumentParser(
        description="Build TugasinWP theme: generate minified CSS bundle and create zip"
    )
    parser.add_argument(
        "--version", "-v",
        help="Version for the zip filename (default: read from style.css)"
    )
    parser.add_argument(
        "--no-minify",
        action="store_true",
        help="Do not minify CSS/JS (keep readable)"
    )
    parser.add_argument(
        "--skip-bundle",
        action="store_true",
        help="Skip CSS bundle generation"
    )
    parser.add_argument(
        "--skip-critical",
        action="store_true",
        help="Skip critical CSS generation"
    )
    parser.add_argument(
        "--skip-js",
        action="store_true",
        help="Skip JS minification"
    )
    parser.add_argument(
        "--skip-zip",
        action="store_true",
        help="Skip zip creation (only generate assets)"
    )
    parser.add_argument(
        "--list-files",
        action="store_true",
        help="List CSS files that would be bundled and exit"
    )
    
    args = parser.parse_args()
    
    print("=" * 50)
    print("🚀 TugasinWP Build Script")
    print("=" * 50)
    
    # Determine paths
    script_dir = get_script_dir()
    # Theme path is parent directory since scripts is in build/ subdirectory
    theme_path = script_dir.parent
    output_path = script_dir / OUTPUT_FOLDER
    
    print(f"\nTheme path: {theme_path}")
    print(f"Build path: {script_dir}")
    
    if not theme_path.exists():
        print(f"\n❌ Error: Theme folder not found at {theme_path}")
        sys.exit(1)
    
    # List files mode
    if args.list_files:
        print("\n📋 CSS files to be bundled:\n")
        css_files = discover_css_files(theme_path)
        for i, f in enumerate(css_files, 1):
            print(f"  {i:2}. {f}")
        print(f"\n   Total: {len(css_files)} files")
        
        print("\n📋 Critical CSS files:\n")
        for i, f in enumerate(CRITICAL_CSS_FILES, 1):
            print(f"  {i:2}. {f}")
        
        print("\n📋 JavaScript files:\n")
        for i, f in enumerate(JS_FILES, 1):
            print(f"  {i:2}. {f}")
        
        sys.exit(0)
    
    # Get version
    version = args.version or get_version_from_style(theme_path)
    print(f"Version: {version}")
    
    minify = not args.no_minify
    
    # Step 1: Generate CSS bundle
    if not args.skip_bundle:
        generate_css_bundle(theme_path, minify=minify)
    else:
        print("\n⏭️  Skipping CSS bundle generation")
    
    # Step 2: Generate Critical CSS
    if not args.skip_critical:
        generate_critical_css(theme_path, minify=minify)
    else:
        print("\n⏭️  Skipping critical CSS generation")
    
    # Step 3: Process JavaScript
    if not args.skip_js:
        generate_js_bundle(theme_path, minify=minify)
    else:
        print("\n⏭️  Skipping JavaScript processing")
    
    # Step 4: Create zip
    if not args.skip_zip:
        zip_path = create_zip(theme_path, output_path, version)
        print(f"\n🎉 Build complete!")
        print(f"   Output: {zip_path}")
    else:
        print("\n⏭️  Skipping zip creation")
        print("\n🎉 Build complete!")
    
    print("=" * 50)


if __name__ == "__main__":
    main()
