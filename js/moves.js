// Move effects
const MoveEffects = {
    // Basic attack - no special effect
    none: (attacker, defender, damage) => {
        return { damage };
    },

    // Multiple hits (Bladestorm)
    multipleHits: (attacker, defender, damage) => {
        const hits = Math.floor(Math.random() * 2) + 2; // 2-3 hits
        const totalDamage = damage * hits;
        return {
            damage: totalDamage,
            message: `Hit ${hits} times!`
        };
    },

    // Increase crit chance (Focus)
    increaseCritChance: (attacker, defender, damage) => {
        attacker.critChance += 0.2;
        return {
            damage: 0,
            message: `${attacker.name}'s critical chance increased!`,
            buff: true
        };
    },

    // Poison effect (Poison Strike)
    poison: (attacker, defender, damage) => {
        defender.poisoned = true;
        defender.poisonDamage = Math.floor(damage * 0.3);
        defender.poisonTurns = 3;
        return {
            damage,
            message: `${defender.name} was poisoned!`
        };
    },

    // Arcane Barrier
    arcaneBarrier: (attacker, defender, damage) => {
        attacker.barrier = true;
        attacker.barrierStrength = 30;
        return {
            damage: 0,
            message: `${attacker.name} is protected by an arcane barrier!`,
            buff: true
        };
    },

    // Blood Frenzy
    bloodFrenzy: (attacker, defender, damage) => {
        attacker.damageMultiplier = (attacker.damageMultiplier || 1) * 1.5;
        return {
            damage: 0,
            message: `${attacker.name} enters a blood frenzy!`,
            buff: true
        };
    }
};

// Map move names to their effects
const MoveEffectMap = {
    'Slash': MoveEffects.none,
    'Bladestorm': MoveEffects.multipleHits,
    'Focus': MoveEffects.increaseCritChance,
    'Poison Strike': MoveEffects.poison,
    'Arcane Barrier': MoveEffects.arcaneBarrier,
    'Blood Frenzy': MoveEffects.bloodFrenzy
};

export { MoveEffects, MoveEffectMap };
