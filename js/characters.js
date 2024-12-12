// Character class definition
class Character {
    constructor(name, maxHp, critChance, accuracy, color, moves = []) {
        this.name = name;
        this.maxHp = maxHp;
        this.currentHp = maxHp;
        this.critChance = critChance;
        this.accuracy = accuracy;
        this.color = color;
        this.moves = moves;
        this.x = 0;
        this.y = 0;
        this.width = 50;
        this.height = 80;
    }

    draw(ctx) {
        ctx.fillStyle = this.color;
        ctx.fillRect(this.x, this.y, this.width, this.height);
    }
}

// Function to get selected character from database
async function getSelectedCharacter() {
    try {
        const response = await fetch('characters.php?get_selected_character=1');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching character:', error);
        return null;
    }
}

// Function to load the selected character
async function loadSelectedCharacter() {
    try {
        const data = await getSelectedCharacter();
        if (!data) {
            throw new Error('No character data received');
        }
        return new Character(
            data.name,
            data.max_hp,
            data.crit_chance,
            data.accuracy,
            data.character_color,
            data.moves
        );
    } catch (error) {
        console.error('Error loading character:', error);
        // Return a default character if loading fails
        return new Character('Default', 100, 0.1, 0.8, '#blue', []);
    }
}

export { Character, getSelectedCharacter, loadSelectedCharacter };