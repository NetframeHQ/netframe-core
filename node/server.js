const fs = require('fs')
const Step = require('prosemirror-transform').Step
const schema = require ('./schema.js')
var mysql = require('mysql')

try {
  require('dotenv').config({path: '../.env'})
} catch (e) {
  console.warn('No ../.env file, starting without environment file');
}

var prodMod = process.env.PROD_MODE || '';

if (prodMod == 'BM') {
    var privateKey  = fs.readFileSync(process.env.APACHE_KEY, 'utf8')
    var certificate = fs.readFileSync(process.env.APACHE_PEM, 'utf8')
    var credentials = {key: privateKey, cert: certificate, passphrase: process.env.PASSPHRASE}
}

// setup socket server
const app = require('express')();
const httpServer = (prodMod == 'BM') ? require('https').Server(credentials, app) : require('http').Server(app);
const io = require('socket.io').listen(httpServer);
const listenHost = process.env.COLLAB_LISTEN_HOST;
const port = process.env.COLLAB_PORT_BACK;

//bd
function getMysqlConnectionOptions() {
  const [ host, port ] = process.env.DB_HOST.split(':');

  const {
    DB_USERNAME: user,
    DB_PASSWORD: password,
    DB_DATABASE: database
  } = process.env;

  const options = {
    host,
    user,
    password,
    database
  };

  if (port) {
    options.port = parseInt(port, 10);
  }

  return options;
}
var connection = mysql.createConnection(getMysqlConnectionOptions());

// const lockedPath = './db_locked.json'
const maxStoredSteps = 1000;

// function storeLocked(locked) {
  // fs.writeFileSync(lockedPath, locked.toString())
// }

// function getLocked() {
  // return JSON.parse(fs.readFileSync(lockedPath, 'utf8'))
// }

async function storeDoc({doc, version, steps}, storedData, docId) {
  const oldData = storedData.steps
  const limitedOldData = oldData.slice(Math.max(oldData.length - maxStoredSteps))
  const newData = [
    ...limitedOldData,
    ...steps.map((step, index) => {
      return {
        step: JSON.parse(JSON.stringify(step)),
        version: version + index + 1,
        clientID: step.clientID,
      }
    })
  ]
  dbQuery("Update colab_docs SET content="+connection.escape(JSON.stringify({doc, version: (version+steps.length), steps: newData}))+" where id="+docId)
}

async function dbQuery(query) {
    try {
      return new Promise(function(resolve, reject) {
        // connection.connect(function(err) {
        //   if (err) reject(err);

          connection.query(query, function (err, result) {
            if (err) return reject(err);
            resolve(result);
          });
        // });
      });
    } catch(err) {
        console.error("async err: " + err);
    }
}

async function getDoc(docId) {
  docId = parseInt(docId, 10);

  if (isNaN(docId)) {
    throw new TypeError('The docId shall be an integer');
  }

  return dbQuery('SELECT * from colab_docs where id=' + docId);
}

async function getSteps(version, docId) {
  let data = await getDoc(docId)
  let doc = data[0].content
  let steps = JSON.parse(doc).steps
  return steps.filter(step => step.version > version)
}

io.sockets.on('connection', async (socket) => {
  socket.join("room-"+socket.handshake.query.docId)
  socket.on('cursor', (data) => {
    // console.log(data)
    io.sockets.in('room-'+socket.handshake.query.docId).emit('cursor', data)
  })
  socket.on('update', async ({ version, clientID, steps }) => {
    // we need to check if there is another update processed
    // so we store a "locked" state
    /*coonst locked = getLocked()

    if (locked) {
      // we will do nothing and wait for another client update
      return
    }

    storeLocked(true)*/
    // console.log(JSON.stringify(steps))
    // await getDoc(socket.handshake.query.docId)
    let data = await getDoc(socket.handshake.query.docId)
    //let last = data[0].created_at
    const storedData = JSON.parse(data[0].content)

    // version mismatch: the stored version is newer
    // so we send all steps of this version back to the user
    if (storedData.version !== version) {
      steps = await getSteps(version, socket.handshake.query.docId)
      socket.emit('update', {
        version,
        steps
      })
      /*storeLocked(false)*/
      return
    }

    let doc = schema.nodeFromJSON(JSON.parse(JSON.stringify(storedData.doc)))
    let newSteps = steps.map(step => {
      const newStep = Step.fromJSON(schema, step)
      newStep.clientID = clientID

      // apply step to document
      let result = newStep.apply(doc)
      doc = result.doc

      return newStep
    })
    // calculating a new version number is easy
    const newVersion = version + newSteps.length

    // store data
    // storeSteps({ version, steps: newSteps })
    await storeDoc({ version: version, doc, steps: newSteps }, storedData, socket.handshake.query.docId)
    steps = await getSteps(version, socket.handshake.query.docId)

    // send update to everyone (me and others)
    io.sockets.in('room-'+socket.handshake.query.docId).emit('update', {
      version: newVersion,
      steps
    })

    /*storeLocked(false)*/
  })

  let data = await getDoc(socket.handshake.query.docId)
  let doc = data[0].content
  let users = []
  try{
    users = JSON.parse(data[0].users)
    if(users===null)
      users = []
  }catch(e){
    console.error(e);
  }
  if(data[0].users_id==socket.handshake.query.userId || users.includes(socket.handshake.query.userId)){
    doc = JSON.parse(doc)
    // send latest document
    socket.emit('init', {doc: doc.doc, version: doc.version, name: data[0].name})
  }else{
    socket.emit('message', {message: 'Vous n\'avez pas accès à ce document'})
  }
  // send client count
  // socket.on('lastSave', (data) => {
  //   let data = await getDoc(socket.handshake.query.docId)
  //   let last = data[0].modified_at
  //   io.sockets.in('room-'+socket.handshake.query.docId).emit('lastSave', last)
  // })
  // io.sockets.in('room-'+socket.handshake.query.docId).emit('getCount', io.sockets.in('room-'+socket.handshake.query.docId).length)
  socket.on('disconnect', () => {
    socket.leave('room-'+socket.handshake.query.docId)
    // io.sockets.emit('getCount', io.engine.clientsCount)
  })
})

httpServer.listen(port, listenHost, () => {
  console.log("Listening on host "+listenHost);
  console.log("Listening on port "+port);
})
