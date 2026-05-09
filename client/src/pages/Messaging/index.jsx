import { useState, useEffect, useRef } from 'react';
import api from '../../api/client';
import useAuthStore from '../../store/useAuthStore';

export default function MessagingPage() {
  const { user } = useAuthStore();
  const [threads, setThreads] = useState([]);
  const [selectedThread, setSelectedThread] = useState(null);
  const [messages, setMessages] = useState([]);
  const [input, setInput] = useState('');
  const [loading, setLoading] = useState(true);
  const messagesRef = useRef(null);

  useEffect(() => { 
    loadThreads(true); 
    const interval = setInterval(() => {
      loadThreads(false);
    }, 5000);
    return () => clearInterval(interval);
  }, []);

  // Poll selected thread messages if one is selected
  useEffect(() => {
    let interval;
    if (selectedThread) {
      interval = setInterval(() => {
        refreshMessages(selectedThread.id);
      }, 5000);
    }
    return () => clearInterval(interval);
  }, [selectedThread]);

  // Auto scroll to bottom when messages update
  useEffect(() => {
    if (messagesRef.current) {
      messagesRef.current.scrollTop = messagesRef.current.scrollHeight;
    }
  }, [messages]);

  const loadThreads = async (showLoading = false) => {
    if (showLoading) setLoading(true);
    try {
      const res = await api.get('/collections/chat_threads');
      const list = (res.data.data || res.data || []).sort((a, b) => new Date(b.lastMessageAt || b.createdAt || 0) - new Date(a.lastMessageAt || a.createdAt || 0));
      setThreads(list);
    } catch (err) { console.error(err); }
    finally { if (showLoading) setLoading(false); }
  };

  const refreshMessages = async (threadId) => {
    try {
      const res = await api.get(`/collections/chat_messages?threadId=${threadId}`);
      const msgs = (res.data.data || res.data || []).sort((a, b) => new Date(a.createdAt || 0) - new Date(b.createdAt || 0));
      setMessages(msgs);
    } catch (err) { console.error(err); }
  };

  const selectThread = async (thread) => {
    setSelectedThread(thread);
    await refreshMessages(thread.id);
    setTimeout(() => { if (messagesRef.current) messagesRef.current.scrollTop = messagesRef.current.scrollHeight; }, 100);
  };

  const sendMessage = async () => {
    if (!input.trim() || !selectedThread) return;
    try {
      await api.post('/collections/chat_messages', {
        threadId: selectedThread.id,
        senderType: 'staff',
        senderUserId: user?.id,
        body: input.trim(),
      });
      setInput('');
      selectThread(selectedThread);
    } catch (err) { console.error(err); }
  };

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>💬 Messaging</h1>
          <p>Percakapan dengan pelanggan dan tim lapangan</p>
        </div>
      </div>

      <div className="glass-card" style={{ padding: 0, overflow: 'hidden', display: 'flex', height: 'calc(100vh - 200px)', minHeight: 400 }}>
        {/* Sidebar */}
        <div style={{ width: 280, borderRight: '1px solid rgba(0,0,0,0.08)', display: 'flex', flexDirection: 'column' }}>
          <div style={{ padding: '12px 14px', fontWeight: 700, fontSize: 12, color: 'var(--text-muted)', textTransform: 'uppercase', borderBottom: '1px solid rgba(0,0,0,0.06)' }}>
            Thread ({threads.length})
          </div>
          <div style={{ flex: 1, overflowY: 'auto' }}>
            {loading ? (
              <div style={{ padding: 20, textAlign: 'center', color: 'var(--text-muted)', fontSize: 13 }}>Memuat...</div>
            ) : threads.length === 0 ? (
              <div style={{ padding: 20, textAlign: 'center', color: 'var(--text-muted)', fontSize: 13 }}>Belum ada percakapan.</div>
            ) : (
              threads.map(thread => (
                <div
                  key={thread.id}
                  onClick={() => selectThread(thread)}
                  style={{
                    padding: '12px 14px',
                    cursor: 'pointer',
                    borderBottom: '1px solid rgba(0,0,0,0.04)',
                    background: selectedThread?.id === thread.id ? 'rgba(29,84,108,0.08)' : 'transparent',
                    borderLeft: selectedThread?.id === thread.id ? '3px solid var(--color-primary)' : '3px solid transparent',
                  }}
                >
                  <div style={{ fontWeight: 600, fontSize: 14 }}>{thread.pelangganNama || thread.subject || 'Thread'}</div>
                  <div style={{ fontSize: 11, color: 'var(--text-muted)', marginTop: 2 }}>
                    {thread.lastMessage?.substring(0, 40) || 'Tidak ada pesan'}
                  </div>
                </div>
              ))
            )}
          </div>
        </div>

        {/* Main Chat */}
        <div style={{ flex: 1, display: 'flex', flexDirection: 'column' }}>
          {!selectedThread ? (
            <div style={{ flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'var(--text-muted)' }}>
              Pilih thread di kiri untuk melihat percakapan.
            </div>
          ) : (
            <>
              <div style={{ padding: '12px 16px', borderBottom: '1px solid rgba(0,0,0,0.06)', fontWeight: 600 }}>
                {selectedThread.pelangganNama || selectedThread.subject || 'Percakapan'}
              </div>
              <div ref={messagesRef} style={{ flex: 1, overflowY: 'auto', padding: 16, display: 'flex', flexDirection: 'column', gap: 8 }}>
                {messages.map(msg => (
                  <div key={msg.id} style={{ alignSelf: msg.senderType === 'staff' ? 'flex-end' : 'flex-start', maxWidth: '75%' }}>
                    <div style={{ fontSize: 10, color: 'var(--text-muted)', marginBottom: 2 }}>{msg.senderType === 'staff' ? 'Admin' : 'Pelanggan'}</div>
                    <div style={{
                      padding: '8px 12px',
                      borderRadius: 12,
                      fontSize: 14,
                      background: msg.senderType === 'staff' ? 'var(--color-primary)' : 'rgba(0,0,0,0.04)',
                      color: msg.senderType === 'staff' ? 'white' : 'var(--text-primary)',
                    }}>
                      {msg.body || ''}
                    </div>
                    <div style={{ fontSize: 9, color: 'var(--text-muted)', marginTop: 2 }}>
                      {msg.createdAt ? new Date(msg.createdAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : ''}
                    </div>
                  </div>
                ))}
                {!messages.length && (
                  <div style={{ textAlign: 'center', color: 'var(--text-muted)', fontSize: 13, padding: 40 }}>Belum ada pesan.</div>
                )}
              </div>
              <div style={{ display: 'flex', gap: 8, padding: 12, borderTop: '1px solid rgba(0,0,0,0.06)' }}>
                <input
                  className="form-input"
                  style={{ flex: 1 }}
                  placeholder="Ketik pesan..."
                  value={input}
                  onChange={(e) => setInput(e.target.value)}
                  onKeyDown={(e) => e.key === 'Enter' && sendMessage()}
                />
                <button className="btn btn-primary btn-sm" onClick={sendMessage}>📤</button>
              </div>
            </>
          )}
        </div>
      </div>
    </>
  );
}
