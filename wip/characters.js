import { Move, AttackMove, BuffMove, Buff } from './Move.js';

export class Character {
  constructor(name, maxHP, critChance = 0.15, accuracy = 0.85, moves = [], critDamage = 1.5) {
    this.name = name;
    this.maxHP = maxHP;
    this.currentHP = maxHP;
    this.critChance = critChance;
    this.accuracy = accuracy;
    this.critDamage = critDamage;
    this.buffs = [];
    this.moves = moves;
  }

  takeDamage(amount) {
    this.currentHP -= amount;
    if (this.currentHP < 0) this.currentHP = 0;
    return this.currentHP === 0;
  }

  addBuff(buff) {
    this.buffs.push(buff);
    buff.apply(this);
  }

  updateBuffs() {
    for (let i = this.buffs.length - 1; i >= 0; i--) {
      const buff = this.buffs[i];
      if (buff.tick()) {
        buff.remove(this);
        this.buffs.splice(i, 1);
      }
    }
  }

  useMove(target, move) {
    return move.execute(this, target);
  }

  heal(amount) {
    this.currentHP = Math.min(this.maxHP, this.currentHP + amount);
  }

  reset() {
    this.currentHP = this.maxHP;
    this.buffs.forEach(buff => buff.remove(this));
    this.buffs = [];
  }
}

// Define moves
const slash = new AttackMove('Slash', 10, 'A basic slashing attack');
const bladestorm = new AttackMove('Bladestorm', 35, 'A powerful spinning attack');
const focus = new BuffMove('Focus', 'Increases critical chance and accuracy', [
  new Buff('critChance', 0.15, 3),
  new Buff('accuracy', 0.15, 3)
]);

const voidStrike = new AttackMove('Void Strike', 25, 'A strike from the void');
const finality = new BuffMove('Finality', 'Increases critical damage', [
  new Buff('critDamage', 0.5, 2)
]);

// Create characters
const redPlayer = new Character('Striker', 100, 0.15, 0.85, [slash, focus, bladestorm]);
const bluePlayer = new Character('Voidling', 140, 0.15, 0.85, [voidStrike, finality]);

export const characters = {
  red: redPlayer,
  blue: bluePlayer
};
