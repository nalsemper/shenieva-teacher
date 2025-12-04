# Complete Narrator Speed Update Script
# This script updates ALL story slides across all levels to use narratorSpeed store

Write-Host "=== Narrator Speed Persistence Update ===" -ForegroundColor Cyan
Write-Host "This will update all story slides to persist narrator speed across slides" -ForegroundColor Yellow
Write-Host ""

$totalFiles = 0
$updated = 0
$skipped = 0

# Get all slide files with narrator speed controls
$slideFiles = Get-ChildItem -Path "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels" -Recurse -Filter "slide_*.svelte" | Where-Object {
    $content = Get-Content $_.FullName -Raw
    $content -match "let speed = "
}

Write-Host "Found $($slideFiles.Count) slides with speed controls" -ForegroundColor Green
Write-Host ""

foreach ($file in $slideFiles) {
    $totalFiles++
    $filePath = $file.FullName
    $relativePath = $filePath -replace [regex]::Escape("c:\xampp\htdocs\shenieva-teacher\"), ""
    
    Write-Host "[$totalFiles/$($slideFiles.Count)] Processing: $relativePath" -ForegroundColor Cyan
    
    $content = Get-Content $filePath -Raw
    $modified = $false
    
    # Step 1: Update import to include narratorSpeed
    if ($content -match 'import \{ language \} from "\$lib/store/story_lang_audio"') {
        Write-Host "  [1/3] Updating import..." -ForegroundColor Yellow
        $content = $content -replace 'import \{ language \} from "\$lib/store/story_lang_audio"', 'import { language, narratorSpeed } from "$lib/store/story_lang_audio"'
        $modified = $true
    }
    elseif ($content -match 'import \{ language, narratorSpeed \}') {
        Write-Host "  [1/3] Import already updated" -ForegroundColor Gray
    }
    else {
        Write-Host "  [1/3] Warning: Cannot find language import" -ForegroundColor Red
    }
    
    # Step 2: Replace local speed variable with store subscription
    if ($content -match "let speed = 'normal';") {
        Write-Host "  [2/3] Updating speed variable..." -ForegroundColor Yellow
        $content = $content -replace "let speed = 'normal';", 'let speed = $narratorSpeed;'
        $modified = $true
    }
    elseif ($content -match 'let speed = \$narratorSpeed;') {
        Write-Host "  [2/3] Speed variable already updated" -ForegroundColor Gray
    }
    else {
        Write-Host "  [2/3] Warning: Cannot find speed variable" -ForegroundColor Red
    }
    
    # Step 3: Update speed buttons - remove radio inputs and add narratorSpeed.set() to onclick
    if ($content -match '<input type="radio" name="speed') {
        Write-Host "  [3/3] Updating speed buttons..." -ForegroundColor Yellow
        
        # Replace Normal button
        $content = $content -replace '(?s)(<label class="chip \{speed === ''normal'' \? ''active'' : ''''\}")[^>]*(on:click="\(\) => \{[^}]*)(speed = ''normal'';)([^}]*\}">)\s*<input[^>]+>\s*(<span class="txt">Normal</span>)', '$1 on:click={() => { narratorSpeed.set(''normal''); speed = ''normal'';$4$5'
        
        # Replace Slow button  
        $content = $content -replace '(?s)(<label class="chip \{speed === ''slow'' \? ''active'' : ''''\}")[^>]*(on:click="\(\) => \{[^}]*)(speed = ''slow'';)([^}]*\}">)\s*<input[^>]+>\s*(<span class="txt">Slow</span>)', '$1 on:click={() => { narratorSpeed.set(''slow''); speed = ''slow'';$4$5'
        
        # Replace Fast button
        $content = $content -replace '(?s)(<label class="chip \{speed === ''fast'' \? ''active'' : ''''\}")[^>]*(on:click="\(\) => \{[^}]*)(speed = ''fast'';)([^}]*\}">)\s*<input[^>]+>\s*(<span class="txt">Fast</span>)', '$1 on:click={() => { narratorSpeed.set(''fast''); speed = ''fast'';$4$5'
        
        # Fallback: Simple pattern without existing on:click
        $content = $content -replace '(?s)<label class="chip \{speed === ''normal'' \? ''active'' : ''''\}">\s*<input type="radio"[^>]+>\s*<span class="txt">Normal</span>\s*</label>', '<label class="chip {speed === ''normal'' ? ''active'' : ''''}" on:click={() => { narratorSpeed.set(''normal''); speed = ''normal''; }}><span class="txt">Normal</span></label>'
        $content = $content -replace '(?s)<label class="chip \{speed === ''slow'' \? ''active'' : ''''\}">\s*<input type="radio"[^>]+>\s*<span class="txt">Slow</span>\s*</label>', '<label class="chip {speed === ''slow'' ? ''active'' : ''''}" on:click={() => { narratorSpeed.set(''slow''); speed = ''slow''; }}><span class="txt">Slow</span></label>'
        $content = $content -replace '(?s)<label class="chip \{speed === ''fast'' \? ''active'' : ''''\}">\s*<input type="radio"[^>]+>\s*<span class="txt">Fast</span>\s*</label>', '<label class="chip {speed === ''fast'' ? ''active'' : ''''}" on:click={() => { narratorSpeed.set(''fast''); speed = ''fast''; }}><span class="txt">Fast</span></label>'
        
        $modified = $true
    }
    elseif ($content -match 'narratorSpeed\.set\(') {
        Write-Host "  [3/3] Buttons already updated" -ForegroundColor Gray
    }
    else {
        Write-Host "  [3/3] Warning: No radio buttons found" -ForegroundColor Red
    }
    
    # Save file if modified
    if ($modified) {
        $content | Set-Content $filePath -NoNewline
        $updated++
        Write-Host "  [OK] Updated successfully" -ForegroundColor Green
    } else {
        $skipped++
        Write-Host "  - No changes needed" -ForegroundColor DarkGray
    }
    
    Write-Host ""
}

Write-Host "=== Update Complete ===" -ForegroundColor Cyan
Write-Host "Total files processed: $totalFiles" -ForegroundColor White
Write-Host "Files updated: $updated" -ForegroundColor Green
Write-Host "Files skipped: $skipped" -ForegroundColor Gray
Write-Host ""
Write-Host 'Note: The narrator speed will now persist across slides within a story!' -ForegroundColor Yellow
Write-Host 'Next step: Add speed reset when exiting entire story (not just slides)' -ForegroundColor Yellow
