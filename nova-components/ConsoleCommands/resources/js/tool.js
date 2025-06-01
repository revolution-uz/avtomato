import Command from './pages/Command'
import Commands from './pages/Commands'

Nova.booting((Vue, store) => {
  Nova.inertia('ConsoleCommand', Command);
  Nova.inertia('ConsoleCommands', Commands)
})
