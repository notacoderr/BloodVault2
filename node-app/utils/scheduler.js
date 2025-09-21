import Agenda from 'agenda';

export function createScheduler({ mongoUrl, processEvery, disabled }) {
  const jobHandlers = new Map();
  let agendaInstance = null;

  if (!disabled) {
    agendaInstance = new Agenda({
      db: { address: mongoUrl },
      processEvery
    });
  } else {
    console.warn('Agenda scheduler disabled via configuration. Background jobs will run inline.');
  }

  async function runInline(name, handler, data) {
    if (!handler) {
      console.warn(`Attempted to run unknown job "${name}" inline.`);
      return;
    }

    try {
      await handler({ attrs: { data: data ?? null } });
    } catch (error) {
      console.error(`Inline execution of job "${name}" failed`, error);
    }
  }

  return {
    define(name, handler) {
      jobHandlers.set(name, handler);
      agendaInstance?.define(name, handler);
    },
    async start() {
      if (!agendaInstance) {
        if (!disabled) {
          console.warn('Agenda scheduler unavailable. Background jobs will run inline until MongoDB is configured.');
        }
        return false;
      }

      try {
        await agendaInstance.start();
        return true;
      } catch (error) {
        console.error('Failed to start agenda scheduler. Falling back to inline execution.', error);
        agendaInstance = null;
        return false;
      }
    },
    async stop() {
      if (!agendaInstance) {
        return;
      }
      try {
        await agendaInstance.stop();
      } catch (error) {
        console.error('Failed to stop agenda scheduler cleanly', error);
      }
    },
    async every(interval, name) {
      if (!agendaInstance) {
        console.warn(`Skipping recurring job "${name}" because the scheduler is not running.`);
        return;
      }
      try {
        await agendaInstance.every(interval, name);
      } catch (error) {
        console.error(`Failed to schedule recurring job "${name}"`, error);
      }
    },
    async now(name, data) {
      const handler = jobHandlers.get(name);
      if (agendaInstance) {
        try {
          await agendaInstance.now(name, data);
          return;
        } catch (error) {
          console.error(`Failed to enqueue job "${name}". Running inline instead.`, error);
        }
      }

      await runInline(name, handler, data);
    }
  };
}

export default createScheduler;
