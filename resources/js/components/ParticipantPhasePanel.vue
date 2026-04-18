<template>
  <div class="card">
    <div class="card-header">
      <span class="card-title">Utilisateurs concernés</span>
      <span class="badge badge-gray">{{ participants.length }}</span>
    </div>

    <div class="card-body">
      <div class="legend">
        <span class="legend-item"><span class="legend-dot dot-done"></span> Participé</span>
        <span class="legend-item"><span class="legend-dot dot-pending"></span> En attente</span>
        <span class="legend-item"><span class="legend-dot dot-missed"></span> Manqué</span>
        <span class="legend-item"><span class="legend-dot dot-na"></span> N/A</span>
      </div>

      <div v-if="participants.length" class="participant-list">
        <div v-for="participant in participants" :key="participant.id" class="participant-row">
          <div class="participant-meta">
            <span class="participant-icon" :class="participant.roleClass">{{ participant.icon }}</span>
            <div class="participant-text">
              <div class="participant-name">{{ participant.name }}</div>
              <div class="participant-role">{{ participant.roleLabel }}</div>
            </div>
          </div>

          <div v-if="showsProgress(participant)" class="phase-bars">
            <div v-for="phase in phases" :key="phase.key" class="phase-item">
              <span class="phase-label">{{ phase.short }}</span>
              <div class="phase-bar" :class="phaseStateClass(participant, phase.key)"></div>
            </div>
          </div>

          <div v-else class="participant-helper">
            Intervient en réponse aux différentes phases
          </div>
        </div>
      </div>

      <div v-else class="text-muted text-xs">Aucun participant disponible pour cette décision.</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  decision: {
    type: Object,
    required: true,
  },
  phaseParticipationMap: {
    type: Object,
    default: () => ({}),
  },
});

const phases = [
  { key: 'clarification', short: 'C', index: 0 },
  { key: 'reaction', short: 'R', index: 1 },
  { key: 'objection', short: 'O', index: 2 },
];

const roleMeta = {
  author: { label: 'Porteur', icon: '💡', className: 'role-author' },
  animator: { label: 'Animateur', icon: '🎭', className: 'role-animator' },
  participant: { label: 'Participant', icon: '👥', className: 'role-participant' },
  excluded: { label: 'Exclu', icon: '🚫', className: 'role-excluded' },
  observer: { label: 'Observateur', icon: '👁', className: 'role-observer' },
};

const currentPhaseIndex = computed(() => {
  const status = props.decision.status;

  if (status === 'draft') return -1;
  if (status === 'clarification') return 0;
  if (status === 'reaction') return 1;
  return 2;
});

const participants = computed(() => {
  const explicitRoles = new Map(
    (props.decision.participants || []).map((participant) => [participant.user_id, participant.role]),
  );

  const members = props.decision.circle?.members || [];

  return members
    .map((member) => {
      const decisionRole = explicitRoles.get(member.user_id);
      const displayRole = decisionRole || (member.role === 'observer' ? 'observer' : 'participant');
      const meta = roleMeta[displayRole] || roleMeta.participant;

      return {
        id: member.user_id,
        name: member.user?.name || 'Utilisateur',
        role: displayRole,
        roleLabel: meta.label,
        icon: meta.icon,
        roleClass: meta.className,
      };
    })
    .sort((left, right) => {
      const order = { author: 0, animator: 1, participant: 2, excluded: 3, observer: 4 };
      return (order[left.role] ?? 9) - (order[right.role] ?? 9) || left.name.localeCompare(right.name);
    });
});

const showsProgress = (participant) => !['author', 'animator'].includes(participant.role);

const isExpectedForPhase = (participant, phaseKey) => {
  if (participant.role === 'author' || participant.role === 'excluded' || participant.role === 'observer') {
    return false;
  }

  if (participant.role === 'animator') {
    return phaseKey === 'clarification';
  }

  return true;
};

const phaseStateClass = (participant, phaseKey) => {
  const phaseIndex = phases.find((phase) => phase.key === phaseKey)?.index ?? -1;
  const expected = isExpectedForPhase(participant, phaseKey);

  if (!expected) {
    return 'state-na';
  }

  if (phaseIndex > currentPhaseIndex.value) {
    return 'state-upcoming';
  }

  const participated = Boolean(props.phaseParticipationMap?.[phaseKey]?.[participant.id]);

  if (phaseIndex === currentPhaseIndex.value) {
    return participated ? 'state-current-done' : 'state-current-pending';
  }

  return participated ? 'state-done' : 'state-missed';
};
</script>

<style scoped>
.legend {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 14px;
  font-size: 11px;
  color: var(--gray-500);
}

.legend-item {
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 999px;
}

.dot-done { background: var(--teal-500); }
.dot-pending { background: var(--amber-600); }
.dot-missed { background: var(--red-600); }
.dot-na { background: var(--gray-300); }

.participant-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.participant-row {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 12px;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  background: var(--gray-50);
}

.participant-meta {
  display: flex;
  align-items: center;
  gap: 10px;
}

.participant-icon {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  border: 1px solid transparent;
}

.role-author { background: var(--blue-50); border-color: var(--blue-200); }
.role-animator { background: var(--amber-50); border-color: var(--amber-100); }
.role-participant { background: var(--teal-50); border-color: var(--teal-100); }
.role-excluded { background: var(--red-50); border-color: var(--red-100); }
.role-observer { background: var(--gray-100); border-color: var(--gray-200); }

.participant-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-900);
}

.participant-role {
  font-size: 11px;
  color: var(--gray-500);
}

.participant-helper {
  font-size: 11px;
  color: var(--gray-500);
  padding-left: 40px;
}

.phase-bars {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 8px;
}

.phase-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.phase-label {
  font-size: 10px;
  font-weight: 700;
  color: var(--gray-500);
  width: 12px;
  flex-shrink: 0;
}

.phase-bar {
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: var(--gray-200);
}

.state-done,
.state-current-done {
  background: var(--teal-500);
}

.state-current-pending {
  background: var(--amber-600);
}

.state-missed {
  background: var(--red-600);
}

.state-na {
  background: linear-gradient(90deg, var(--gray-200), var(--gray-300));
}

.state-upcoming {
  background: var(--gray-100);
  border: 1px dashed var(--gray-300);
}
</style>
