<script>
  import { onMount } from 'svelte';

  let player = { x: 100, y: 100, direction: 'front', frame: 0, speed: 5 };
  let trashItems = [
    { id: 1, x: 300, y: 400, collected: false },
    { id: 2, x: 600, y: 150, collected: false },
  ];
  let obstacles = [
    { type: 'tree', x: 200, y: 200 },
    { type: 'tree', x: 500, y: 300 },
    { type: 'house', x: 350, y: 350 },
  ];
  let score = 0;

  const assetsPath = '/SRC/ASSETS/TRASH_COLLECT_GAME';
  const playerSprites = {
    front: [
      `${assetsPath}/boy/walking_sprite/walking_front/1.png`,
      `${assetsPath}/boy/walking_sprite/walking_front/2.png`,
      `${assetsPath}/boy/walking_sprite/walking_front/3.png`
    ],
    back: [
      `${assetsPath}/boy/walking_sprite/walking_back/1.png`,
      `${assetsPath}/boy/walking_sprite/walking_back/2.png`,
      `${assetsPath}/boy/walking_sprite/walking_back/3.png`
    ],
    left: [
      `${assetsPath}/boy/walking_sprite/walking_left/1.png`,
      `${assetsPath}/boy/walking_sprite/walking_left/2.png`,
      `${assetsPath}/boy/walking_sprite/walking_left/3.png`
    ],
    right: [
      `${assetsPath}/boy/walking_sprite/walking_right/1.png`,
      `${assetsPath}/boy/walking_sprite/walking_right/2.png`,
      `${assetsPath}/boy/walking_sprite/walking_right/3.png`
    ],
  };

  const movePlayer = (direction) => {
    let newX = player.x;
    let newY = player.y;

    if (direction === 'up') newY -= player.speed;
    if (direction === 'down') newY += player.speed;
    if (direction === 'left') newX -= player.speed;
    if (direction === 'right') newX += player.speed;

    // Check for obstacle collisions
    const isColliding = obstacles.some(
      (obstacle) =>
        Math.abs(obstacle.x - newX) < 50 && Math.abs(obstacle.y - newY) < 50
    );

    // Update position if no collision
    if (!isColliding) {
      player.x = newX;
      player.y = newY;
      player.direction = direction;
      player.frame = (player.frame + 1) % 3; // Cycle sprite frame
    }

    // Check for trash collection
    trashItems.forEach((item) => {
      if (
        !item.collected &&
        Math.abs(player.x - item.x) < 30 &&
        Math.abs(player.y - item.y) < 30
      ) {
        item.collected = true;
        score += 10;
      }
    });
  };

  onMount(() => {
    window.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowUp') movePlayer('back');
      if (event.key === 'ArrowDown') movePlayer('front');
      if (event.key === 'ArrowLeft') movePlayer('left');
      if (event.key === 'ArrowRight') movePlayer('right');
    });
  });
</script>

<style>
  body {
    margin: 0;
    padding: 0;
  }
  #game {
    position: relative;
    width: 100vw;
    height: 100vh;
    background: url('/SRC/ASSETS/TRASH_COLLECT_GAME/ground/soil.png') repeat;
  }
  .player {
    position: absolute;
    width: 50px;
    height: 50px;
  }
  .trash {
    position: absolute;
    width: 30px;
    height: 30px;
  }
  .obstacle {
    position: absolute;
    width: 50px;
    height: 50px;
  }
</style>

<div id="game">
  <div
    class="player"
    style="left: {player.x}px; top: {player.y}px; background-image: url({playerSprites[player.direction][player.frame]});"
  ></div>
  {#each trashItems as item}
    {#if !item.collected}
      <div
        class="trash"
        style="left: {item.x}px; top: {item.y}px; background-image: url('/SRC/ASSETS/TRASH_COLLECT_GAME/trash/0{item.id}-Egg.png');"
      ></div>
    {/if}
  {/each}
  {#each obstacles as obstacle}
    <div
      class="obstacle"
      style="left: {obstacle.x}px; top: {obstacle.y}px; background-image: url('/SRC/ASSETS/TRASH_COLLECT_GAME/{obstacle.type}/{Math.ceil(Math.random() * 3)}.png');"
    ></div>
  {/each}
</div>
<p>Score: {score}</p>
