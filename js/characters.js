// Character construction
// Constructor de presonaje
class Character {
    constructor(name, maxHp, critChance, accuracy, color, moves = []) {
        this.name = name;
        this.maxHp = maxHp;
        this.currentHp = maxHp;
        this.critChance = critChance;
        this.accuracy = accuracy;
        this.color = color;
        this.moves = moves;
        //visual elements for fillRect
        //elementos visuales para fillRect
        this.x = 0;
        this.y = 0;
        this.width = 50;
        this.height = 80;
    }

    //draw method to draw the character in canvas
    //metodo de dibujar para dibujar el personaje en el canvas
    draw(ctx) {
        ctx.fillStyle = this.color;
        ctx.fillRect(this.x, this.y, this.width, this.height);
    }
}

// Function to get selected character from database
// Funcion para obtener el personaje elegido desde la db
async function getSelectedCharacter() {
    try {
        //consulta al characters.php mediante GET
        //query search in character.php through GET
        const response = await fetch('characters.php?get_selected_character=1');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
        return null;
    }
}

// Function to load the selected character
// Función para cargar el personaje seleccionado
async function loadSelectedCharacter() {
    try {
        //llamamos la funcion de obtener el personaje
        //call getcharacter function
        const data = await getSelectedCharacter();
        if (!data) {
            console.error('Error');
        }
        //creamos el personaje mediante la consulta y lo devolvemos a la llamada de función
        //create the character through the query and return it
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
        //devuelve un personaje de por defecto
        //return default char
        return new Character('Default', 100, 0.1, 0.8, '#blue', []);
    }
}

//export the class and it's methods
export { Character, getSelectedCharacter, loadSelectedCharacter };