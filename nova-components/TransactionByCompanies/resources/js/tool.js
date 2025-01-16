import Tool from './pages/Tool'
import Transactions from './pages/Transactions.vue';

Nova.booting((app, store) => {
  Nova.inertia('TransactionByCompanies', Tool)
  Nova.inertia('Transactions', Transactions)
})
