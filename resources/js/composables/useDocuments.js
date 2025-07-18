import { ref } from 'vue';

const documents = ref([]);

function addDocument(doc) {
  documents.value.unshift(doc);
}

function receiveDocument(docId) {
  const doc = documents.value.find(d => d.id === docId);
  if (doc && !doc.dateReceived) {
    const now = new Date();
    doc.dateReceived = now.toLocaleDateString();
    doc.timeReceived = now.toLocaleTimeString();
  }
}

function setDocuments(docs) {
  documents.value = docs;
}

export function useDocuments() {
  return {
    documents,
    addDocument,
    receiveDocument,
    setDocuments
  };
} 