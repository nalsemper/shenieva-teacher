# PowerShell script to update all story slides to use narratorSpeed store
# This makes narrator speed persist across slides within a story

$slides = @(
    # Level 1 Story 1-2
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-2\slide_1.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-2\slide_2.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-2\slide_3.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-2\slide_5.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-2\slide_8.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-2\slide_9.svelte",
    
    # Level 1 Story 1-3
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-3\slide_1.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-3\slide_2.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-3\slide_3.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-3\slide_5.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-3\slide_8.svelte",
    "c:\xampp\htdocs\shenieva-teacher\src\routes\student\Levels\Level1\story1-3\slide_9.svelte"
)

foreach ($file in $slides) {
    if (Test-Path $file) {
        Write-Host "Processing: $file" -ForegroundColor Cyan
        
        $content = Get-Content $file -Raw
        $modified = $false
        
        # Step 1: Add narratorSpeed to import if not present
        if ($content -match 'import \{ language \} from "\$lib/store/story_lang_audio"') {
            Write-Host "  - Updating import statement" -ForegroundColor Yellow
            $content = $content -replace 'import \{ language \} from "\$lib/store/story_lang_audio"', 'import { language, narratorSpeed } from "$lib/store/story_lang_audio"'
            $modified = $true
        }
        
        # Step 2: Replace local speed variable with store subscription
        if ($content -match "let speed = 'normal';") {
            Write-Host "  - Updating speed variable" -ForegroundColor Yellow
            $content = $content -replace "let speed = 'normal';", 'let speed = $narratorSpeed;'
            $modified = $true
        }
        
        # Step 3: Remove radio inputs and add on:click handlers
        if ($content -match '<input type="radio" name="speed') {
            Write-Host "  - Updating speed buttons" -ForegroundColor Yellow
            
            # Pattern 1: Normal button
            $content = $content -replace '(?s)<label class="chip \{speed === ''normal'' \? ''active'' : ''''\}">\s*<input type="radio" name="speed\w*" bind:group=\{speed\} value="normal" />\s*<span class="txt">Normal</span>\s*</label>', '<label class="chip {speed === ''normal'' ? ''active'' : ''''}" on:click={() => { narratorSpeed.set(''normal''); speed = ''normal''; }}><span class="txt">Normal</span></label>'
            
            # Pattern 2: Slow button
            $content = $content -replace '(?s)<label class="chip \{speed === ''slow'' \? ''active'' : ''''\}">\s*<input type="radio" name="speed\w*" bind:group=\{speed\} value="slow" />\s*<span class="txt">Slow</span>\s*</label>', '<label class="chip {speed === ''slow'' ? ''active'' : ''''}" on:click={() => { narratorSpeed.set(''slow''); speed = ''slow''; }}><span class="txt">Slow</span></label>'
            
            # Pattern 3: Fast button
            $content = $content -replace '(?s)<label class="chip \{speed === ''fast'' \? ''active'' : ''''\}">\s*<input type="radio" name="speed\w*" bind:group=\{speed\} value="fast" />\s*<span class="txt">Fast</span>\s*</label>', '<label class="chip {speed === ''fast'' ? ''active'' : ''''}" on:click={() => { narratorSpeed.set(''fast''); speed = ''fast''; }}><span class="txt">Fast</span></label>'
            
            $modified = $true
        }
        
        # Save file if modified
        if ($modified) {
            $content | Set-Content $file -NoNewline
            Write-Host "  ✓ Updated successfully" -ForegroundColor Green
        } else {
            Write-Host "  - No changes needed" -ForegroundColor Gray
        }
    } else {
        Write-Host "File not found: $file" -ForegroundColor Red
    }
}

Write-Host "`nScript complete!" -ForegroundColor Green
